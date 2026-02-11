<?php

namespace App\Entity;

use App\Repository\DestinationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: DestinationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Destination
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $excerpt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    // image optionnelle (url externe OU chemin upload)
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $thumb = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_DRAFT;

    // ✅ Destination de la semaine
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isWeekly = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $weeklyRank = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\Column(length: 500, nullable: true)]
    private ?string $link = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(
        targetEntity: Media::class,
        mappedBy: 'destination',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $media;

    // ---- VICH UPLOAD (image) ----
    #[Vich\UploadableField(mapping: 'destination_upload', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = self::STATUS_DRAFT;
        $this->isWeekly = false;
        $this->media = new ArrayCollection();
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

    public function __toString(): string
    {
        return (string) ($this->title ?? 'Destination #' . $this->id);
    }

    // -------- getters / setters --------

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

    public function getCity(): ?string
    {
        return $this->city;
    }
    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }
    public function setExcerpt(?string $excerpt): static
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
    public function setContent(?string $content): static
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

    public function isWeekly(): bool
    {
        return $this->isWeekly;
    }
    public function setIsWeekly(bool $isWeekly): static
    {
        $this->isWeekly = $isWeekly;
        return $this;
    }

    public function getWeeklyRank(): ?int
    {
        return $this->weeklyRank;
    }
    public function setWeeklyRank(?int $weeklyRank): static
    {
        $this->weeklyRank = $weeklyRank;
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

    public function getThumb(): ?string
    {
        // priorité à l’upload Vich
        if ($this->imageName) {
            return '/uploads/destinations/' . $this->imageName;
        }
        // fallback url externe
        return $this->thumb;
    }

    public function setThumb(?string $thumb): static
    {
        $this->thumb = $thumb;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): static
    {
        $this->imageFile = $imageFile;

        // forcer Doctrine à voir un changement
        if ($imageFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;
        return $this;
    }


    /** @return Collection<int, Media> */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setDestination($this);
        }
        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            if ($medium->getDestination() === $this) {
                $medium->setDestination(null);
            }
        }
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;
        return $this;
    }
}
