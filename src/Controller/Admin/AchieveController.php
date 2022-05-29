<?php

namespace App\Controller\Admin;

use App\Entity\Achieve;
use App\Event\AdminCRUDEvent;
use App\Form\Admin\AchieveType;
use App\Form\Filter\AdminAchieveType;
use App\Model\Admin\AchieveSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AchieveController extends AbstractController
{
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $search = new AchieveSearch();
        $form = $this->createForm(AdminAchieveType::class, $search, ['em' => $em]);

        $form->handleRequest($request);

        $qb = $em->getRepository(Achieve::class)->getAdmins($search);

        $achieves = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/achieve/index.html.twig', [
            'achieves' => $achieves,
            'searchForm' => $form->createView(),
        ]);
    }

    public function create(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $achieve = new Achieve();
        $form = $this->createForm(AchieveType::class, $achieve);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($achieve);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($achieve);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une réalisation a été crée');

            return $this->redirectToRoute('app_admin_achieve_index');
        }

        return $this->render('admin/achieve/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Achieve $achieve): Response
    {
        $form = $this->createForm(AchieveType::class, $achieve);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($achieve);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une réalisation a été mise à jour');

            return $this->redirectToRoute('app_admin_achieve_index');
        }

        return $this->render('admin/achieve/edit.html.twig', [
            'form' => $form->createView(),
            'achieve' => $achieve,
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
        Achieve $achieve
    )
    {
        if ($request->query->has('pos')) {
            $pos = ($achieve->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $achieve->setPosition($pos);
                $em->flush();

                $this->addFlash('success', 'La position de la réalisation a été modifier');
            }
        }

        return  $this->redirectToRoute('app_admin_achieve_index');
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Achieve $achieve)
    {
        $form = $this->deleteForm($achieve);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($achieve);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($achieve);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Une réalisation a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la réalisation n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette réalisation ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $achieve,
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
                    $achieve = $em->getRepository(Achieve::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($achieve), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($achieve);
                }

                $em->flush();

                $this->addFlash('success', 'Les réalisations ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les réalisations n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' réalisations ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette réalisation ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Achieve $achieve)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_achieve_delete', ['id' => $achieve->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_achieve_bulk_delete'))
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


