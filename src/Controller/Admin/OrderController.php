<?php

namespace App\Controller\Admin;

use App\Entity\Command;
use App\Entity\Hostel;
use App\Entity\User;
use App\Event\AdminCRUDEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $qb = $em->getRepository(Command::class)->findBy([], ['createdAt' => 'desc']);

        $orders = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function show(EntityManagerInterface $em, $id)
    {
        $order = $em->getRepository(Command::class)->find($id);

        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    public function byUser(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        User $user)
    {
        $qb = $em->getRepository(Command::class)->findBy(
            ['user' => $user], ['createdAt' => 'desc']);

        $orders = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function hostel(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        Hostel $hostel)
    {
        $qb = $em->getRepository(Command::class)->findBy(
            ['hostel' => $hostel], ['createdAt' => 'desc']);

        $orders = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        $id)
    {
        $order = $em->getRepository(Command::class)->find($id);

        $form = $this->deleteForm($order);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($order);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($order);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'La Order a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la Order n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette Order ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $order,
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
                    $order = $em->getRepository(Command::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($order), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($order);
                }

                $em->flush();

                $this->addFlash('info', 'Les Orders ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les Orders n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' commamdes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette Order ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Command $order)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_order_delete', ['id' => $order->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_order_bulk_delete'))
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


