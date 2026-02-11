<?php

namespace App\Entity;

use App\Repository\PremiumBriefRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PremiumBriefRepository::class)]
#[ORM\Table(name: 'premium_briefs')]
#[ORM\HasLifecycleCallbacks]
class PremiumBrief
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $title;

    #[ORM\Column(length: 190, unique: true)]
    private string $slug;

    /**
     * "afrique" | "region" | "pays" | "theme"
     */
    #[ORM\Column(length: 30)]
    private string $scope = 'afrique';

    /**
     * exemple: "Afrique de l’Ouest", "Sahel", "RDC", "Économie"
     */
    #[ORM\Column(length: 120, nullable: true)]
    private ?string $scopeLabel = null;

    /**
     * HTML (WYSIWYG) : ton contenu premium lisible + formaté
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $summaryHtml = null;

    /**
     * Liste de points clés: ["Ce qui s'est passé", "Pourquoi c'est important", ...]
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $bullets = [];

    /**
     * Tags simples: ["sahel","sécurité","élections"]
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $tags = [];

    /**
     * draft | published
     */
    #[ORM\Column(length: 20)]
    private string $status = 'draft';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ---------------- GETTERS / SETTERS ----------------

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getScope(): string { return $this->scope; }
    public function setScope(string $scope): self { $this->scope = $scope; return $this; }

    public function getScopeLabel(): ?string { return $this->scopeLabel; }
    public function setScopeLabel(?string $scopeLabel): self { $this->scopeLabel = $scopeLabel; return $this; }

    public function getSummaryHtml(): ?string { return $this->summaryHtml; }
    public function setSummaryHtml(?string $summaryHtml): self { $this->summaryHtml = $summaryHtml; return $this; }

    public function getBullets(): ?array { return $this->bullets; }
    public function setBullets(?array $bullets): self { $this->bullets = $bullets ?? []; return $this; }

    public function getTags(): ?array { return $this->tags; }
    public function setTags(?array $tags): self { $this->tags = $tags ?? []; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getPublishedAt(): ?\DateTimeImmutable { return $this->publishedAt; }
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self { $this->publishedAt = $publishedAt; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
