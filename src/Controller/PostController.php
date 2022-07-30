<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Settings;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
        Breadcrumbs $breadcrumbs,
        EntityManagerInterface $em,
        $slug
    )
    {
        if (!$this->settings->isActivePost())  throw $this->createNotFoundException('Page introuvable');

        /** @var Post $post */
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug, 'online' => true]);

        if(!$post) throw $this->createNotFoundException('L\'article n\'existe pas');

        $this->breadcrumb($breadcrumbs)->addItem($post->getTitle());
        $posts = $em->getRepository(Post::class)->findRecentBySlug(3, $slug);

        return $this->render('site/post/show.html.twig', [
            'post' => $post,
            'posts' => $posts,
            'settings' => $this->settings,
        ]);
    }

    /**
     * @param Breadcrumbs $breadcrumbs
     * @return Breadcrumbs
     */
    public function breadcrumb(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Acceuil', $this->generateUrl('app_home'))
            ->addItem('Actualités', $this->generateUrl('app_post_index'));

        return $breadcrumbs;
    }
}


