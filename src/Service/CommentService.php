<?php

namespace App\Service;

use App\Auth\AuthService;
use App\Entity\Comment;
use App\Entity\Content;
use App\Event\CommentCreatedEvent;
use App\Model\CommentData;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class CommentService
{
    private AuthService $auth;

    private EntityManagerInterface $em;

    private EventDispatcherInterface $dispatcher;

    public function __construct(AuthService $auth, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->auth = $auth;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function create(CommentData $data): Comment
    {
        // On crée un nouveau commentaire
        /** @var Content $target */
        $target = $this->em->getRepository(Content::class)->find($data->target);
        /** @var Comment|null $parent */
        $parent = $data->parent ? $this->em->getReference(Comment::class, $data->parent) : null;
        $comment = (new Comment())
            ->setAuthor($this->auth->getUserOrNull())
            ->setUsername($data->username)
            ->setCreatedAt(new DateTime())
            ->setContent($data->content)
            ->setParent($parent)
            ->setTarget($target);
        $this->em->persist($comment);
        $this->em->flush();
        $this->dispatcher->dispatch(new CommentCreatedEvent($comment));

        return $comment;
    }

    public function update(Comment $comment, string $content): Comment
    {
        $comment->setContent($content);
        $this->em->flush();

        return $comment;
    }

    public function delete(int $commentId): void
    {
        /** @var Comment $comment */
        $comment = $this->em->getReference(Comment::class, $commentId);
        $this->em->remove($comment);
        $this->em->flush();
    }
}



