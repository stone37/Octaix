<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminCustomerType;
use App\Form\RegistrationAdminType;
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

class AdminController extends AbstractController
{
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $search = new CustomerSearch();

        $form = $this->createForm(AdminCustomerType::class, $search);

        $form->handleRequest($request);

        $qb = $em->getRepository(User::class)->getAdmins($search);

        $admins = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/admin/index.html.twig', [
            'admins' => $admins,
            'searchForm' => $form->createView(),
        ]);
    }

    public function create(
        Request $request,
        UserManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher)
    {
        $admin = $manager->createUser();

        $form = $this->createForm(RegistrationAdminType::class, $admin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($admin);

            $admin->setPassword(
                $form->has('plainPassword') ? $passwordEncoder->encodePassword(
                    $admin,
                    $form->get('plainPassword')->getData()
                ) : ''
            );

            $admin->setCreatedAt(new DateTime());
            $admin->setNotificationsReadAt(new DateTimeImmutable());

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $manager->updateUser($admin);

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('info', 'Un compte admin a été crée');

            return $this->redirectToRoute('app_admin_admin_index');
        }

        return $this->render('admin/admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher, $id): Response
    {
        $admin = $em->getRepository(User::class)->find($id);
        $form = $this->createForm(RegistrationAdminType::class, $admin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($admin);

            if ($form->get('plainPassword')->getData()) {
                $admin->setPassword(
                    $form->has('plainPassword') ? $passwordEncoder->encodePassword(
                        $admin,
                        $form->get('plainPassword')->getData()
                    ) : ''
                );
            }

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('info', 'Un compte admin a été mise à jour');

            return $this->redirectToRoute('app_admin_admin_index');
        }

        return $this->render('admin/admin/edit.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin,
        ]);
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        $id)
    {
        $admin = $em->getRepository(User::class)->find($id);

        $form = $this->deleteForm($admin);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($admin);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($admin);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'Le compte admin a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le admin coursier n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $admin,
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
                    $admin = $em->getRepository(User::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($admin), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($admin);
                }

                $em->flush();

                $this->addFlash('info', 'Les comptes admin ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les comptes admin n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' comptes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_admin_delete', ['id' => $user->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_admin_bulk_delete'))
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

