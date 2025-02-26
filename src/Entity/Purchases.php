<?php

namespace App\Entity;

use App\Repository\PurchasesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchasesRepository::class)]
class Purchases
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "purchases")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Lessons::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Lessons $lesson = null;

    #[ORM\ManyToOne(targetEntity: Formations::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Formations $formation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getLesson(): ?Lessons
    {
        return $this->lesson;
    }

    public function setLesson(?Lessons $lesson): static
    {
        $this->lesson = $lesson;
        return $this;
    }

    public function getFormation(): ?Formations
    {
        return $this->formation;
    }

    public function setFormation(?Formations $formation): static
    {
        $this->formation = $formation;
        return $this;
    }
}
