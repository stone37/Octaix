<?php

namespace App\Storage;

use App\Entity\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderSessionStorage
{
    private const ORDER_KEY_NAME = 'orderId';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->em = $entityManager;
        $this->session = $session;
    }

    public function set(string $orderId): void
    {
        $this->session->set(self::ORDER_KEY_NAME, $orderId);
    }

    public function remove(): void
    {
        $this->session->remove(self::ORDER_KEY_NAME);
    }

    public function getOrderById(): ?Command
    {
        if ($this->has()) {
            return $this->em->getRepository(Command::class)->findOneById($this->get());
        }

        return null;
    }

    public function has(): bool
    {
        return $this->session->has(self::ORDER_KEY_NAME);
    }

    public function get(): string
    {
        return $this->session->get(self::ORDER_KEY_NAME);
    }
}

