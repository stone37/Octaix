<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Event\AdminCRUDEvent;
use App\Form\Admin\ReviewType;
use App\Form\Filter\AdminReviewType;
use App\Model\Admin\ReviewSearch;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReviewController extends AbstractController
{
    public function index(
        Request $request,
        ReviewRepository $reviewRepository,
        PaginatorInterface $paginator)
    {
        $search = new ReviewSearch();
        $form = $this->createForm(AdminReviewType::class, $search);

        $form->handleRequest($request);

        $qb = $reviewRepository->getAdmins($search);

        $reviews = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/review/index.html.twig', [
            'reviews' => $reviews,
            'searchForm' => $form->createView(),
        ]);
    }

    public function create(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($review);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($review);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un témoignage a été crée');

            return $this->redirectToRoute('app_admin_review_index');
        }

        return $this->render('admin/review/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Review $review): Response
    {
        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($review);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un témoignage a été mise à jour');

            return $this->redirectToRoute('app_admin_review_index');
        }

        return $this->render('admin/review/edit.html.twig', [
            'form' => $form->createView(),
            'review' => $review,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $id
     * @return RedirectResponse
     */
    public function move(
        Request $request,
        EntityManagerInterface $em,
        Review $review
    )
    {
        if ($request->query->has('pos')) {
            $pos = ($review->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $review->setPosition($pos);
                $em->flush();

                $this->addFlash('success', 'La position du témoignage a été modifier');
            }
        }

        return  $this->redirectToRoute('app_admin_review_index');
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Review $review)
    {
        $form = $this->deleteForm($review);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($review);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($review);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Un témoignage a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le témoignage n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet témoignage ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $review,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    public function deleteBulk(
        Request $request,
        EntityManagerInterface $em,
        SessionInterface $session,
        EventDispatcherInterface $dispatcher
    )
    {
        $ids = $request->query->get('data');

        if ($request->query->has('data'))
            $session->set('data', $request->query->get('data'));

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $session->get('data');
                $session->remove('data');

                foreach ($ids as $id) {
                    $review = $em->getRepository(Review::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($review), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($review);
                }

                $em->flush();

                $this->addFlash('success', 'Les témoignages ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les témoignages n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' témoignages ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet témoignage ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Review $review)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_review_delete', ['id' => $review->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_review_bulk_delete'))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function configuration()
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ]
            ]
        ];
    }
}


