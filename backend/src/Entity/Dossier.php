<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
#[ORM\Table(name: 'dossier')]
#[ORM\Index(columns: ['slug'], name: 'idx_dossier_slug')]
#[ORM\Index(columns: ['status'], name: 'idx_dossier_status')]
#[ORM\Index(columns: ['published_at'], name: 'idx_dossier_published_at')]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Dossier
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(length: 255, unique: true)]
    private string $slug = '';

    // petit chapo (HTML possible)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lead = null;

    // corps (HTML possible, multi-paragraphes)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    // conclusion (HTML possible)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $conclusion = null;

    #[ORM\Column(length: 255)]
    private string $authorName = 'Rédaction';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $authorBio = null;

    // fallback URL externe OU chemin manuel (si pas d’upload)
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $thumb = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Un dossier regroupe plusieurs articles
     */
    #[ORM\ManyToMany(targetEntity: Article::class)]
    #[ORM\JoinTable(name: 'dossier_article')]
    private Collection $articles;


    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: DossierReaction::class, orphanRemoval: true)]
    private Collection $reactions;


    // ---- VICH UPLOAD (cover image) ----
    #[Vich\UploadableField(mapping: 'dossier_upload', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->reactions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = self::STATUS_DRAFT;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        if ($this->createdAt === null) $this->createdAt = $now;
        if ($this->updatedAt === null) $this->updatedAt = $now;
        if ($this->status === '' || $this->status === null) $this->status = self::STATUS_DRAFT;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
        if ($this->status === '' || $this->status === null) $this->status = self::STATUS_DRAFT;
    }

    // -------- getters/setters --------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getLead(): ?string
    {
        return $this->lead;
    }

    public function setLead(?string $lead): self
    {
        $this->lead = $lead;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getConclusion(): ?string
    {
        return $this->conclusion;
    }

    public function setConclusion(?string $conclusion): self
    {
        $this->conclusion = $conclusion;
        return $this;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): self
    {
        $this->authorName = $authorName;
        return $this;
    }

    public function getAuthorBio(): ?string
    {
        return $this->authorBio;
    }

    public function setAuthorBio(?string $authorBio): self
    {
        $this->authorBio = $authorBio;
        return $this;
    }

    public function getThumb(): ?string
    {
        // priorité à l’upload Vich
        if ($this->imageName) {
            return '/uploads/dossiers/' . $this->imageName;
        }
        return $this->thumb;
    }

    public function setThumb(?string $thumb): self
    {
        $this->thumb = $thumb;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $allowed = [self::STATUS_DRAFT, self::STATUS_PUBLISHED];
        $this->status = in_array($status, $allowed, true) ? $status : self::STATUS_DRAFT;
        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /** @return Collection<int, Article> */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
        }
        return $this;
    }

    public function removeArticle(Article $article): self
    {
        $this->articles->removeElement($article);
        return $this;
    }

    // -------- Vich image --------

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        // IMPORTANT : forcer Doctrine à voir un changement
        if ($imageFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }

    /** @return Collection<int, DossierReaction> */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(DossierReaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setDossier($this);
        }
        return $this;
    }

    public function removeReaction(DossierReaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            if ($reaction->getDossier() === $this) {
                $reaction->setDossier(null);
            }
        }
        return $this;
    }
}
