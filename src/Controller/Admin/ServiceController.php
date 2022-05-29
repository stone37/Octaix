<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Event\AdminCRUDEvent;
use App\Form\Admin\ServiceType;
use App\Form\Filter\AdminServiceType;
use App\Model\Admin\ServiceSearch;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ServiceController extends AbstractController
{
    public function index(
        Request $request,
        ServiceRepository $serviceRepository,
        PaginatorInterface $paginator
    )
    {
        $search = new ServiceSearch();
        $form = $this->createForm(AdminServiceType::class, $search);

        $form->handleRequest($request);

        if ($request->query->has('parentId'))
            $qb = $serviceRepository->getAdmins($search, $request->query->get('parentId'));
        else
            $qb = $serviceRepository->getAdmins($search);

        $services = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        $parent = ($request->query->has('parentId')) ? $serviceRepository->find($request->query->get('parentId')): null;

        return $this->render('admin/service/index.html.twig', [
            'services' => $services,
            'parentId' => $request->query->get('parentId'),
            'parent'   => $parent,
            'searchForm' => $form->createView(),
        ]);
    }

    public function create(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    )
    {
        $parent = null;

        if ($request->query->has('parentId'))
            $parent = $em->getRepository(Service::class)->find($request->query->get('parentId'));

        $service = new Service();
        $service->setParent($parent);

        $form = $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($service);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($service);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un service a été crée');

            $redirect = ($request->query->has('parentId')) ? $this->redirectToRoute('app_admin_service_index', ['parentId' => $request->query->get('parentId')]) : $this->redirectToRoute('app_admin_service_index');

            return $redirect;
        }

        return $this->render('admin/service/create.html.twig', [
            'form' => $form->createView(),
            'parent' => $parent,
            'parentId' => $request->query->get('parentId'),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Service $service
    )
    {
        $form = $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($service);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un service a été mise à jour');

            $redirect = ($request->query->has('parentId')) ? $this->redirectToRoute('app_admin_service_index', ['parentId' => $request->query->get('parentId')]) : $this->redirectToRoute('app_admin_service_index');

            return $redirect;
        }

        return $this->render('admin/service/edit.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
            'parent' => $service->getParent(),
            'parentId' => $request->query->get('parentId'),
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
        Service $service
    )
    {
        if ($request->query->has('pos')) {
            $pos = ($service->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $service->setPosition($pos);
                $em->flush();

                $this->addFlash('success', 'La position du service a été modifier');
            }
        }

        $redirect = ($request->query->has('parentId')) ? $this->redirectToRoute('app_admin_service_index', ['parentId' => $request->query->get('parentId')]) : $this->redirectToRoute('app_admin_service_index');

        return $redirect;
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Service $service
    )
    {
        $form = $this->deleteForm($service);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($service);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($service);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le service a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le service n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet service ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $service,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param SessionInterface $session
     * @return JsonResponse|RedirectResponse
     */
    public function deleteBulk(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        SessionInterface $session
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
                    $service = $em->getRepository(Service::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($service), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($service);
                }

                $em->flush();

                $this->addFlash('success', 'Les services ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les services n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' services ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet service ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    /**
     * @param Service $service
     * @return \Symfony\Component\Form\FormInterface
     */
    private function deleteForm(Service $service)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_service_delete', ['id' => $service->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_service_bulk_delete'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @return \string[][][]
     */
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

