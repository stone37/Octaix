<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminPostType;
use App\Form\Admin\PostType;
use App\Model\Admin\PostSearch;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class PostController extends AbstractController
{
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $search = new PostSearch();

        $form = $this->createForm(AdminPostType::class, $search, ['em' => $em]);

        $form->handleRequest($request);

        $qb = $em->getRepository(Post::class)->getAdminPosts($search);

        $posts = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/post/index.html.twig', [
            'posts' => $posts,
            'searchForm' => $form->createView(),
        ]);
    }

    public function create(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($post);

            if ($post->isOnline() && !$post->getPublishedAt()) {
                $post->setPublishedAt(new DateTime());
            }

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($post);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('info', 'Un article a été crée');

            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render('admin/post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher, $id): Response
    {
        $post = $em->getRepository(Post::class)->find($id);
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($post);

            if ($post->isOnline() && !$post->getPublishedAt()) {
                $post->setPublishedAt(new DateTime());
            }

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('info', 'Un article a été mise à jour');

            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render('admin/post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        $id)
    {
        $post = $em->getRepository(Post::class)->find($id);

        $form = $this->deleteForm($post);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($post);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($post);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'L\'article a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'article n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet article ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $post,
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
                    $post = $em->getRepository(Post::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($post), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($post);
                }

                $em->flush();

                $this->addFlash('info', 'Les articles ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les articles n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' articles ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet article ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_post_delete', ['id' => $post->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_post_bulk_delete'))
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




