<?php

namespace App\Entity;

use App\Repository\LessonsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: LessonsRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class Lessons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $video_url = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(targetEntity: Formations::class, inversedBy: "lessons")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formations $formation = null;

    #[ORM\OneToMany(mappedBy: "lesson", targetEntity: Progress::class, cascade: ["persist", "remove"])]
    private Collection $progress;

    #[ORM\OneToMany(mappedBy: "lesson", targetEntity: Certifications::class, cascade: ["persist", "remove"])]
    private Collection $certifications;

    public function __construct()
    {
        $this->progress = new ArrayCollection();
        $this->certifications = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->video_url;
    }

    public function setVideoUrl(string $video_url): static
    {
        $this->video_url = $video_url;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
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

    /**
     * @return Collection<int, Progress>
     */
    public function getProgress(): Collection
    {
        return $this->progress;
    }

    public function addProgress(Progress $progress): static
    {
        if (!$this->progress->contains($progress)) {
            $this->progress->add($progress);
            $progress->setLesson($this);
        }

        return $this;
    }

    public function removeProgress(Progress $progress): static
    {
        if ($this->progress->removeElement($progress)) {
            if ($progress->getLesson() === $this) {
                $progress->setLesson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Certifications>
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certifications $certification): static
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setLesson($this);
        }

        return $this;
    }

    public function removeCertification(Certifications $certification): static
    {
        if ($this->certifications->removeElement($certification)) {
            if ($certification->getLesson() === $this) {
                $certification->setLesson(null);
            }
        }

        return $this;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }
}
