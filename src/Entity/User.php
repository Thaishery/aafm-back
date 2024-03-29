<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use stdClass;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isInternal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: '0', nullable: true)]
    private ?string $externalId = null;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Articles::class, orphanRemoval: true)]
    private Collection $articles;

    #[ORM\ManyToMany(targetEntity: Activitees::class, mappedBy: 'user')]
    private Collection $activitees;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Adhesion $id_adhesion = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->activitees = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isIsInternal(): ?bool
    {
        return $this->isInternal;
    }

    public function setIsInternal(bool $isInternal): static
    {
        $this->isInternal = $isInternal;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuteur($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuteur() === $this) {
                $article->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activitees>
     */
    public function getActivitees(): Collection
    {
        return $this->activitees;
    }

    public function addActivitee(Activitees $activitee): static
    {
        if (!$this->activitees->contains($activitee)) {
            $this->activitees->add($activitee);
            $activitee->addUser($this);
        }

        return $this;
    }

    public function removeActivitee(Activitees $activitee): static
    {
        if ($this->activitees->removeElement($activitee)) {
            $activitee->removeUser($this);
        }

        return $this;
    }

    public function getIdAdhesion(): ?Adhesion
    {
        return $this->id_adhesion;
    }

    public function setIdAdhesion(?Adhesion $id_adhesion): static
    {
        $this->id_adhesion = $id_adhesion;

        return $this;
    }

    public function getPopulatedActivitees(){
        $activitees = $this->getActivitees();
        $result = [];
        foreach($activitees as $activitee){
            $result[] = $activitee->populatefromUser();
        }
        return $result;
    }

    public function populate(){
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'firstname'=>$this->getFirstname(),
            'lastname'=>$this->getLastname(),
            'adhesion'=>(!null == $this->getIdAdhesion())?$this->getIdAdhesion()->populate():new stdClass,
            'articles'=>$this->getArticles(),
            'activitees'=>$this->getPopulatedActivitees(),
        ];
    }
}
