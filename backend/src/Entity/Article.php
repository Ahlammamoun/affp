<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $excerpt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 20)]
    private string $status = 'draft';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    // ✅ IMPORTANT: nullable en PHP pour éviter crash si un form envoie null,
    // mais en DB ça reste NOT NULL. PrePersist va forcer la valeur.
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(
        targetEntity: Media::class,
        mappedBy: 'article',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $media;

    /**
     * @var Collection<int, ArticleReaction>
     */
    #[ORM\OneToMany(targetEntity: ArticleReaction::class, mappedBy: 'article')]
    private Collection $reactions;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Section $section = null;


    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isMustRead = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $mustReadRank = null;

    public function __construct()
    {
        // pas obligatoire, mais aide
        $this->createdAt = new \DateTimeImmutable();
        $this->media = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();

        // ✅ BÉTON: si un form/admin a mis createdAt à null => on force
        if ($this->createdAt === null) {
            $this->createdAt = $now;
        }

        if ($this->updatedAt === null) {
            $this->updatedAt = $now;
        }

        if ($this->status === '' || $this->status === null) {
            $this->status = 'draft';
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
        if ($this->status === '' || $this->status === null) {
            $this->status = 'draft';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): static
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    // ✅ retourne non-null (car PrePersist force)
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt ?? new \DateTimeImmutable();
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setArticle($this);
        }
        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            if ($medium->getArticle() === $this) {
                $medium->setArticle(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ArticleReaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(ArticleReaction $reaction): static
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setArticle($this);
        }
        return $this;
    }

    public function removeReaction(ArticleReaction $reaction): static
    {
        if ($this->reactions->removeElement($reaction)) {
            if ($reaction->getArticle() === $this) {
                $reaction->setArticle(null);
            }
        }
        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): static
    {
        $this->section = $section;
        return $this;
    }

    public function isMustRead(): bool
    {
        return $this->isMustRead;
    }

    public function setIsMustRead(bool $isMustRead): static
    {
        $this->isMustRead = $isMustRead;
        return $this;
    }

    public function getMustReadRank(): ?int
    {
        return $this->mustReadRank;
    }

    public function setMustReadRank(?int $mustReadRank): static
    {
        $this->mustReadRank = $mustReadRank;
        return $this;
    }

    public function __toString(): string
    {
        return (string) ($this->title ?? 'Article #' . $this->id);
    }
}
