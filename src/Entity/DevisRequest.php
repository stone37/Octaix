<?php

namespace App\Entity;

use App\Repository\DevisRequestRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use geertw\IpAnonymizer\IpAnonymizer;

/**
 * Sauvegarde les demandes de devis afin de limiter le spam.
 *
 * Class DevisRequest
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=DevisRequestRepository::class)
 */
class DevisRequest
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

    public function setId(?int $id): DevisRequest
    {
        $this->id = $id;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip):DevisRequest
    {
        $this->ip = $ip;

        return $this;
    }

    public function setRawIp(?string $ip): DevisRequest
    {
        $this->ip = (new IpAnonymizer())->anonymize($ip);

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): DevisRequest
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}


