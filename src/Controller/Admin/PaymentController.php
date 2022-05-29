<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Event\AdminCRUDEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PaymentController extends AbstractController
{
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $qb = $em->getRepository(Payment::class)->findBy([], ['createdAt' => 'desc']);

        $payments = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/payment/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        $id)
    {
        $payment = $em->getRepository(Payment::class)->find($id);

        $form = $this->deleteForm($payment);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($payment);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($payment);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'Le paiement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le paiement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet paiement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $payment,
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
                    $payment = $em->getRepository(Payment::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($payment), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($payment);
                }

                $em->flush();

                $this->addFlash('info', 'Les paiements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les paiements n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' epaiements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet paiement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Payment $payment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payment_delete', ['id' => $payment->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payment_bulk_delete'))
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




