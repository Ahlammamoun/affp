<?php

namespace App\Entity;

use App\Repository\ArticleReactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Article;


#[ORM\Entity(repositoryClass: ArticleReactionRepository::class)]
#[ORM\Table(name: 'article_reaction')]
#[ORM\Index(columns: ['target_type', 'target_id'], name: 'reaction_target_idx')]
#[ORM\Index(columns: ['fingerprint'], name: 'reaction_fp_idx')]
#[ORM\UniqueConstraint(name: 'reaction_unique_like', columns: ['fingerprint', 'target_type', 'target_id'])]
#[ORM\HasLifecycleCallbacks]




class ArticleReaction
{
    public const TYPE_ARTICLE = 'article';
    public const TYPE_DOSSIER = 'dossier';

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

    // ✅ cible générique (article/dossier)
    #[ORM\Column(name: 'target_type', length: 20)]
    private string $targetType = self::TYPE_ARTICLE;

    #[ORM\Column(name: 'target_id', type: Types::INTEGER)]
    private int $targetId = 0;


    #[ORM\ManyToOne(inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Article $article = null;
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->value = 1;
        $this->targetType = self::TYPE_ARTICLE;
        $this->targetId = 0;
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

        if ($this->targetType === '' || $this->targetType === null) {
            $this->targetType = self::TYPE_ARTICLE;
        }
    }

    // ---------- GETTERS / SETTERS ----------

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

    public function getTargetType(): string
    {
        return $this->targetType;
    }

    public function setTargetType(string $targetType): static
    {
        $this->targetType = $targetType;
        return $this;
    }

    public function getTargetId(): int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): static
    {
        $this->targetId = $targetId;
        return $this;
    }

    // Helpers optionnels
    public function isForArticle(): bool
    {
        return $this->targetType === self::TYPE_ARTICLE;
    }

    public function isForDossier(): bool
    {
        return $this->targetType === self::TYPE_DOSSIER;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;
        return $this;
    }
}
