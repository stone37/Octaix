<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Settings;
use App\Form\CommentType;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class PostController extends AbstractController
{
    /**
     * @var Settings
     */
    private $settings;

    public function __construct(SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public function index(
        Request $request,
        EntityManagerInterface $em,
        Breadcrumbs $breadcrumbs,
        PaginatorInterface $paginator)
    {
        if (!$this->settings->isActivePost())  throw $this->createNotFoundException('Page introuvable');

        $this->breadcrumb($breadcrumbs);

        $data = $em->getRepository(Post::class)->getPosts();
        $posts = $paginator->paginate($data, $request->query->getInt('page', 1), 15);

        return $this->render('site/post/index.html.twig', [
            'posts' => $posts,
            'settings' => $this->settings,
        ]);
    }

    /**
     * @param Request $request
     * @param SettingsManager $manager
     * @param Breadcrumbs $breadcrumbs
     * @param EntityManagerInterface $em
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(
        Request $request,
        Breadcrumbs $breadcrumbs,
        ReCaptcha $reCaptcha,
        EntityManagerInterface $em,
        $slug)
    {
        if (!$this->settings->isActivePost())  throw $this->createNotFoundException('Page introuvable');

        /** @var Post $post */
        $post = $em->getRepository(Post::class)->getPost($slug);

        if(!$post) throw $this->createNotFoundException('L\'article n\'existe pas');

        $this->breadcrumb($breadcrumbs)->addItem($post->getTitle());

        $comment = (new Comment())->setTarget($post);

        if ($this->getUser()) {
            $comment->setEmail($this->getUser()->getEmail());
            $comment->setUsername($this->getUser()->getUsername());
        }

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($reCaptcha->verify($form['recaptchaToken']->getData())->isSuccess()) {

                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Merci pour votre commentaire');
            } else {
                $this->addFlash('error', 'Error de validation du message');
            }

            $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('site/post/show.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'settings' => $this->settings,
        ]);
    }

    public function partial(EntityManagerInterface $em)
    {
        if (!$this->settings->isActivePost())  throw $this->createNotFoundException('Page introuvable');

        $posts = $em->getRepository(Post::class)->findRecent(5);

        return $this->render('site/post/partial.html.twig', [
            'settings' => $this->settings,
            'posts' => $posts,
        ]);
    }

    public function category(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        Breadcrumbs $breadcrumbs,
        $slug)
    {
        if (!$this->settings->isActivePost())  throw $this->createNotFoundException('Page introuvable');

        /** @var Category $category */
        $category = $em->getRepository(Category::class)->getBySlug($slug);
        if(!$category ) throw $this->createNotFoundException('L\'article n\'existe pas');

        $this->breadcrumb($breadcrumbs)->addItem($category->getName());

        $posts = $em->getRepository(Post::class)->getByCategory($category);
        $posts = $paginator->paginate($posts, $request->query->getInt('page', 1), 15);

        return $this->render('site/post/category.html.twig', [
            'settings' => $this->settings,
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    /**
     * @param Breadcrumbs $breadcrumbs
     * @return Breadcrumbs
     */
    public function breadcrumb(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Acceuil', $this->generateUrl('app_home'))
            ->addItem('Blog', $this->generateUrl('app_post_index'));

        return $breadcrumbs;
    }
}


