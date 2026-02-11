<?php

namespace App\Entity;

use App\Repository\DossierReactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierReactionRepository::class)]
#[ORM\Table(name: 'dossier_reaction')]
#[ORM\UniqueConstraint(name: 'uniq_dossier_fp', columns: ['dossier_id', 'fingerprint'])]
#[ORM\Index(columns: ['fingerprint'], name: 'dossier_reaction_fp_idx')]
#[ORM\HasLifecycleCallbacks]
class DossierReaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 1 = like (tu peux étendre plus tard)
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    private int $value = 1;

    #[ORM\Column(length: 64)]
    private string $fingerprint = '';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Dossier $dossier = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->value = 1;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }

        if ($this->value === 0) {
            $this->value = 1;
        }
    }

    // ---------------- GETTERS / SETTERS ----------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getFingerprint(): string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(string $fingerprint): static
    {
        $this->fingerprint = $fingerprint;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt ?? new \DateTimeImmutable();
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        return $this;
    }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): static
    {
        $this->dossier = $dossier;
        return $this;
    }
}
