<?php

namespace App\Entity;

use App\Repository\LiveUpdateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiveUpdateRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LiveUpdate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Ex: "Live", "Alerte" (optionnel)
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $tag = null;

    // Ex: "Crise au Sahel : les dernières informations"
    #[ORM\Column(length: 255)]
    private string $title;

    // Optionnel: lien vers un article interne (si tu veux)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Article $article = null;

    // Optionnel: lien externe
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $url = null;

    // Pour afficher l’heure (et trier)
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $happenedAt = null;

    // Permet de désactiver une ligne sans supprimer
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt ??= $now;
        $this->happenedAt ??= $now;
    }

    public function getId(): ?int { return $this->id; }

    public function getTag(): ?string { return $this->tag; }
    public function setTag(?string $tag): self { $this->tag = $tag; return $this; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getArticle(): ?Article { return $this->article; }
    public function setArticle(?Article $article): self { $this->article = $article; return $this; }

    public function getUrl(): ?string { return $this->url; }
    public function setUrl(?string $url): self { $this->url = $url; return $this; }

    public function getHappenedAt(): \DateTimeImmutable { return $this->happenedAt ?? new \DateTimeImmutable(); }
    public function setHappenedAt(?\DateTimeImmutable $dt): self { $this->happenedAt = $dt; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): self { $this->isActive = $v; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt ?? new \DateTimeImmutable(); }
}
