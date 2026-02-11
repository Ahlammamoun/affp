<?php

namespace App\Entity;

use App\Repository\FeaturedSlotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeaturedSlotRepository::class)]
#[ORM\Table(name: 'featured_slot')]
class FeaturedSlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Clé unique du slot
     * ex: portrait_du_jour, article_une, breaking_news
     */
    #[ORM\Column(name: 'slot_key', type: 'string', length: 255)]
    private ?string $slotKey = null;


    /**
     * Article lié au slot
     */
    #[ORM\ManyToOne(targetEntity: Article::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Article $article = null;

 

    /* ================= GETTERS / SETTERS ================= */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlotKey(): string
    {
        return $this->slotKey;
    }

    public function setSlotKey(string $slotKey): self
    {
        $this->slotKey = $slotKey;
        return $this;
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
