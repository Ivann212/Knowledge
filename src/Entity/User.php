<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Purchases::class, cascade: ["persist", "remove"])]
    private Collection $purchases;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Progress::class, cascade: ["persist", "remove"])]
    private Collection $progress;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Certifications::class, cascade: ["persist", "remove"])]
    private Collection $certifications;

    #[ORM\ManyToMany(targetEntity: Formations::class)]
    #[ORM\JoinTable(name: 'user_formations')]
    private Collection $purchasedFormations;

    #[ORM\ManyToMany(targetEntity: Lessons::class)]
    #[ORM\JoinTable(name: 'user_lessons')]
    private Collection $purchasedLessons;

    #[ORM\ManyToMany(targetEntity: Formations::class)]
    #[ORM\JoinTable(name: "user_completed_formations")]
    private Collection $completedFormations;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $updatedBy = null;


    public function __construct()
    {
        $this->purchases = new ArrayCollection();
        $this->progress = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->purchasedFormations = new ArrayCollection();
        $this->purchasedLessons = new ArrayCollection();
        $this->completedFormations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchases $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setUser($this);
        }
        return $this;
    }

    public function removePurchase(Purchases $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            if ($purchase->getUser() === $this) {
                $purchase->setUser(null);
            }
        }
        return $this;
    }

    public function getProgress(): Collection
    {
        return $this->progress;
    }

    public function addProgress(Progress $progress): static
    {
        if (!$this->progress->contains($progress)) {
            $this->progress->add($progress);
            $progress->setUser($this);
        }
        return $this;
    }

    public function removeProgress(Progress $progress): static
    {
        if ($this->progress->removeElement($progress)) {
            if ($progress->getUser() === $this) {
                $progress->setUser(null);
            }
        }
        return $this;
    }

    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certifications $certification): static
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setUser($this);
        }
        return $this;
    }

    public function removeCertification(Certifications $certification): static
    {
        if ($this->certifications->removeElement($certification)) {
            if ($certification->getUser() === $this) {
                $certification->setUser(null);
            }
        }
        return $this;
    }

    public function getPurchasedFormations(): Collection
    {
        return $this->purchasedFormations;
    }

    public function addPurchasedFormation(Formations $formation): self
    {
        if (!$this->purchasedFormations->contains($formation)) {
            $this->purchasedFormations[] = $formation;
        }
        return $this;
    }

    public function getpurchasedLessons(): Collection
    {
        return $this->purchasedLessons;
    }

    public function addPurchasedLesson(Lessons $lesson): self
    {
        if (!$this->purchasedLessons->contains($lesson)) {
            $this->purchasedLessons[] = $lesson;
        }
        return $this;
    }

    public function getCompletedFormations(): Collection
    {
        return $this->completedFormations;
    }

    public function addCompletedFormation(Formations $formation): self
    {
        if (!$this->completedFormations->contains($formation)) {
            $this->completedFormations->add($formation);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
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

    public function removePurchasedFormation(Formations $purchasedFormation): static
    {
        $this->purchasedFormations->removeElement($purchasedFormation);

        return $this;
    }

    public function removePurchasedLesson(Lessons $purchasedLesson): static
    {
        $this->purchasedLessons->removeElement($purchasedLesson);

        return $this;
    }

    public function removeCompletedFormation(Formations $completedFormation): static
    {
        $this->completedFormations->removeElement($completedFormation);

        return $this;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?self
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?self $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

}