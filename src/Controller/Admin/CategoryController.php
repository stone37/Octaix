<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Event\AdminCRUDEvent;
use App\Form\Admin\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CategoryController extends AbstractController
{
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $qb = $em->getRepository(Category::class)->findBy([], ['name' => 'asc']);

        $categories = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    public function create(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($category);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($category);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('info', 'Une catégorie a été crée');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher, $id): Response
    {
        $category = $em->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($category);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('info', 'Une catégorie a été mise à jour');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        $id)
    {
        $category = $em->getRepository(Category::class)->find($id);

        $form = $this->deleteForm($category);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($category);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($category);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'La catégorie a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la catégorie n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette catégorie ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $category,
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
                    $category = $em->getRepository(Category::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($category), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($category);
                }

                $em->flush();

                $this->addFlash('info', 'Les catégories ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les catégories n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' catégories ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette catégorie ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_category_delete', ['id' => $category->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_category_bulk_delete'))
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




