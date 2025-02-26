<?php

namespace App\Entity;

use App\Repository\ProgressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgressRepository::class)]
class Progress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isComplete = null;

    #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: "progress")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lessons $lesson = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "progress")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(bool $isComplete): static
    {
        $this->isComplete = $isComplete;
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
}
