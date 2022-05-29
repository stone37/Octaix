<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Event\CustomerCreatedEvent;
use App\Form\Filter\AdminCustomerType;
use App\Form\RegistrationCustomerType;
use App\Manager\UserManager;
use App\Model\Admin\CustomerSearch;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CustomerController
 * @package App\Controller\Admin
 */
class CustomerController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $search = new CustomerSearch();

        $form = $this->createForm(AdminCustomerType::class);

        $form->handleRequest($request);

        $qb = $em->getRepository(User::class)->getCustomers($search);

        $customers = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/customer/index.html.twig', [
            'customers' => $customers,
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserManager $manager
     * @param EventDispatcherInterface $dispatcher
     * @return RedirectResponse|Response
     */
    public function create(
        Request $request,
        UserManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher)
    {
        $customer = $manager->createUser();

        $form = $this->createForm(RegistrationCustomerType::class, $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($customer);

            $customer->setPassword(
                $form->has('plainPassword') ? $passwordEncoder->encodePassword(
                    $customer,
                    $form->get('plainPassword')->getData()
                ) : ''
            );

            $customer->setCreatedAt(new DateTime());
            $customer->setNotificationsReadAt(new DateTimeImmutable());

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $manager->updateUser($customer);

            $dispatcher->dispatch(new CustomerCreatedEvent($customer, $form->has('plainPassword')));
            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('info', 'Un compte client a été crée');

            return $this->redirectToRoute('app_admin_customer_index');
        }

        return $this->render('admin/customer/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param $id
     * @return Response
     */
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher,
        User $customer): Response
    {
        $form = $this->createForm(RegistrationCustomerType::class, $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($customer);
            $passwordText = $form->get('plainPassword')->getData();

            if ($passwordText) {
                $customer->setPassword(
                    $form->has('plainPassword') ? $passwordEncoder->encodePassword(
                        $customer,
                        $passwordText
                    ) : ''
                );
            }

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            if ($passwordText) {
                $dispatcher->dispatch(new customerCreatedEvent($customer, $form->has('plainPassword')));
            }

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un compte client a été mise à jour');

            return $this->redirectToRoute('app_admin_customer_index');
        }

        return $this->render('admin/customer/edit.html.twig', [
            'form' => $form->createView(),
            'customer' => $customer,
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param $id
     * @return Response
     */
    public function show(User $customer)
    {
        return $this->render('admin/customer/show.html.twig', [
            'customer' => $customer,
        ]);
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
        User $customer)
    {
        $form = $this->deleteForm($customer);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($customer);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($customer);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'Un compte client a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, un compte client n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet compte client ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $customer,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @param EventDispatcherInterface $dispatcher
     * @return JsonResponse|RedirectResponse
     */
    public function deleteBulk(
        Request $request,
        EntityManagerInterface $em,
        SessionInterface $session,
        EventDispatcherInterface $dispatcher
    )
    {
        $ids = (array) $request->query->get('data');

        if ($request->query->has('data'))
            $session->set('data', $request->query->get('data'));

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $session->get('data');
                $session->remove('data');

                foreach ($ids as $id) {
                    $customer = $em->getRepository(User::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($customer), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($customer);
                }

                $em->flush();

                $this->addFlash('info', 'Les comptes clients ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les comptes clients n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' comptes client ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet compte client ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(User $customer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_customer_delete', ['id' => $customer->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_customer_bulk_delete'))
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

