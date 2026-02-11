<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'uniq_user_email', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column]
    private string $password = '';

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $premiumUntil = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeCustomerId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSubscriptionId = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $stripeSubscriptionStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripePriceId = null;


    #[ORM\Column(length: 64, nullable: true)]
    private ?string $resetPasswordTokenHash = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetPasswordExpiresAt = null;



    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = mb_strtolower(trim($email));
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /** @return string[] */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        // admin => premium auto
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $roles[] = 'ROLE_PREMIUM';
        }

        // ✅ premiumUntil => ROLE_PREMIUM
        if ($this->premiumUntil !== null && $this->premiumUntil > new \DateTimeImmutable()) {
            $roles[] = 'ROLE_PREMIUM';
        }


        return array_values(array_unique($roles));
    }

    /** @param string[] $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getPremiumUntil(): ?\DateTimeImmutable
    {
        return $this->premiumUntil;
    }

    public function setPremiumUntil(?\DateTimeImmutable $dt): self
    {
        $this->premiumUntil = $dt;
        return $this;
    }

    public function isPremium(): bool
    {
        if ($this->isAdmin()) return true;
        return $this->premiumUntil !== null && $this->premiumUntil > new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $id): self
    {
        $this->stripeCustomerId = $id;
        return $this;
    }

    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function setStripeSubscriptionId(?string $id): self
    {
        $this->stripeSubscriptionId = $id;
        return $this;
    }

    public function getStripeSubscriptionStatus(): ?string
    {
        return $this->stripeSubscriptionStatus;
    }

    public function setStripeSubscriptionStatus(?string $status): self
    {
        $this->stripeSubscriptionStatus = $status;
        return $this;
    }

    public function getStripePriceId(): ?string
    {
        return $this->stripePriceId;
    }

    public function setStripePriceId(?string $priceId): self
    {
        $this->stripePriceId = $priceId;
        return $this;
    }


    public function getResetPasswordTokenHash(): ?string
    {
        return $this->resetPasswordTokenHash;
    }

    public function setResetPasswordTokenHash(?string $hash): self
    {
        $this->resetPasswordTokenHash = $hash;
        return $this;
    }

    public function getResetPasswordExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordExpiresAt;
    }

    public function setResetPasswordExpiresAt(?\DateTimeImmutable $dt): self
    {
        $this->resetPasswordExpiresAt = $dt;
        return $this;
    }
}
