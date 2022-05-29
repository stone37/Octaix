<?php

namespace App\Entity;

use App\Entity\Traits\Notifiable;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\UserTrait;
use App\Entity\Traits\SocialLoggableTrait;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class User
 * @package App\Entity
 *
 * @ORM\MappedSuperclass
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"phone"}, message="Il existe déjà un compte avec cet numéro de téléphone.")
 * @UniqueEntity(fields={"email"}, repositoryMethod="findByCaseInsensitive", message="Il existe déjà un compte avec cet e-mail.")
 * @UniqueEntity(fields={"username"}, repositoryMethod="findByCaseInsensitive")
 * @Vich\Uploadable
 */
class User implements UserInterface, Serializable
{
    use IdTrait;
    use MediaTrait;
    use UserTrait;
    use Notifiable;
    use SocialLoggableTrait;
    use DeletableTrait;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank(
     *     message="Entrez une adresse e-mail s'il vous plait.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @Assert\Length(
     *     min="2",
     *     max="180",
     *     minMessage="L'adresse e-mail est trop courte.",
     *     maxMessage="L'adresse e-mail est trop longue.",
     *     groups={"Registration", "Profile"}
     * )
     *
     * @Assert\Email(
     *     message="L''adresse e-mail est invalide.",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $username = '';

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     */
    private $password = '';

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bannedAt = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationToken = null;

    /**
     * @ORM\Column(type="string", options={"default": null}, nullable=true)
     */
    private $lastLoginIp = null;

    /**
     * @ORM\Column(type="datetime", options={"default": null}, nullable=true)
     */
    private $lastLoginAt = null;

    /**
     * @ORM\Column(type="string", options={"default":null}, nullable=true)
     */
    private $invoiceInfo = null;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->__constructUser();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = trim($username ?: '');

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getBannedAt(): ?DateTime
    {
        return $this->bannedAt;
    }

    /**
     * @param DateTime|null $bannedAt
     */
    public function setBannedAt(?DateTime $bannedAt): self
    {
        $this->bannedAt = $bannedAt;

        return $this;
    }

    public function isBanned(): bool
    {
        return null !== $this->bannedAt;
    }

    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string|null $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    /**
     * @param string|null $lastLoginIp
     */
    public function setLastLoginIp(?string $lastLoginIp): self
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    /**
     * @param DateTimeInterface|null $lastLoginAt
     */
    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInvoiceInfo(): ?string
    {
        return $this->invoiceInfo;
    }

    /**
     * @param string|null $invoiceInfo
     */
    public function setInvoiceInfo(?string $invoiceInfo): self
    {
        $this->invoiceInfo = $invoiceInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->password,
        ] = unserialize($serialized);
    }
}

