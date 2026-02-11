<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;


#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]

class Event
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public const CAT_MUSIQUE = 'musique';
    public const CAT_SPORT = 'sport';
    public const CAT_CULTURE = 'culture';
    public const CAT_CONFERENCE = 'conference';
    public const CAT_COMPETITION = 'competition';
    public const CAT_AUTRE = 'autre';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 30)]
    private string $category = self::CAT_AUTRE;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $eventAt = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $link = null;

    // image optionnelle (url externe OU chemin /uploads/...)
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $thumb = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // ---- VICH UPLOAD (image) ----

    #[Vich\UploadableField(mapping: 'event_upload', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = self::STATUS_DRAFT;
        $this->category = self::CAT_AUTRE;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        if ($this->createdAt === null) $this->createdAt = $now;
        if ($this->updatedAt === null) $this->updatedAt = $now;
        if ($this->status === '' || $this->status === null) $this->status = self::STATUS_DRAFT;
        if ($this->category === '' || $this->category === null) $this->category = self::CAT_AUTRE;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
        if ($this->status === '' || $this->status === null) $this->status = self::STATUS_DRAFT;
        if ($this->category === '' || $this->category === null) $this->category = self::CAT_AUTRE;
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

    public function getCategory(): string
    {
        return $this->category;
    }
    public function setCategory(string $category): static
    {
        $this->category = $category;
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

    public function getEventAt(): ?\DateTimeImmutable
    {
        return $this->eventAt;
    }
    public function setEventAt(\DateTimeImmutable $eventAt): static
    {
        $this->eventAt = $eventAt;
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

    public function getThumb(): ?string
    {
        // priorité à l’upload Vich
        if ($this->imageName) {
            return '/uploads/events/' . $this->imageName;
        }

        // fallback si tu as une url externe (thumb)
        return $this->thumb;
    }

    public function setThumb(?string $thumb): static
    {
        $this->thumb = $thumb;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): static
    {
        $this->description = $description;
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
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): static
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

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;
        return $this;
    }
}
