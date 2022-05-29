<?php

namespace App\Controller\Admin;

use App\Entity\Offer;
use App\Event\AdminCRUDEvent;
use App\Form\Admin\OfferType;
use App\Form\Filter\AdminOfferType;
use App\Model\Admin\OfferSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OfferController extends AbstractController
{
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $search = new OfferSearch();

        $form = $this->createForm(AdminOfferType::class, $search);

        $form->handleRequest($request);

        $qb = $em->getRepository(Offer::class)->getAdmins($search);

        $offers = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/offer/index.html.twig', [
            'offers' => $offers,
            'searchForm' => $form->createView(),
        ]);
    }

    public function create(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($offer);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($offer);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('info', 'Une offre a été crée');

            return $this->redirectToRoute('app_admin_offer_index');
        }

        return $this->render('admin/offer/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher, $id): Response
    {
        $offer = $em->getRepository(Offer::class)->find($id);
        $form = $this->createForm(OfferType::class, $offer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($offer);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('info', 'Une offre a été mise à jour');

            return $this->redirectToRoute('app_admin_offer_index');
        }

        return $this->render('admin/offer/edit.html.twig', [
            'form' => $form->createView(),
            'offer' => $offer,
        ]);
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        $id)
    {
        $offer = $em->getRepository(Offer::class)->find($id);

        $form = $this->deleteForm($offer);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($offer);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($offer);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'L\'offre a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'offre n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette offre ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $offer,
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
                    $offer = $em->getRepository(Offer::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($offer), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($offer);
                }

                $em->flush();

                $this->addFlash('info', 'Les offres ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les offres n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' offres ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette offre ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Offer $offer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_offer_delete', ['id' => $offer->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_offer_bulk_delete'))
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

