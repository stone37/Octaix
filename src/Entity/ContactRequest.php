<?php

namespace App\Entity;

use App\Repository\ContactRequestRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use geertw\IpAnonymizer\IpAnonymizer;

/**
 * Sauvegarde les demandes de contact afin de limiter le spam.
 *
 * Class ContactRequest
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=ContactRequestRepository::class)
 */
class ContactRequest
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $ip = '';

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ContactRequest
    {
        $this->id = $id;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): ContactRequest
    {
        $this->ip = $ip;

        return $this;
    }

    public function setRawIp(?string $ip): ContactRequest
    {
        $this->ip = (new IpAnonymizer())->anonymize($ip);

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): ContactRequest
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}


