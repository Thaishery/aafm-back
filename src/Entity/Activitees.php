<?php

namespace App\Entity;

use App\Repository\ActiviteesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiviteesRepository::class)]
class Activitees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $places = null;

    #[ORM\Column]
    private ?bool $is_open = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lieu = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'activitees')]
    private Collection $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(int $places): static
    {
        $this->places = $places;

        return $this;
    }

    public function isIsOpen(): ?bool
    {
        return $this->is_open;
    }

    public function setIsOpen(bool $is_open): static
    {
        $this->is_open = $is_open;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function populate(User $user){
        $users = $this->getUser();
        $inscrit = false;
        foreach($users as $activityUser){
            if($activityUser->getId() == $user->getId()){
                $inscrit = true;
            }
        }
        return [
            'id'=>$this->getId(),
            'date'=>$this->getDate()->getTimestamp(),
            'nom'=>$this->getNom(),
            'description'=>$this->getDescription(),
            'places'=>$this->getPlaces(),
            'is_open'=>$this->isIsOpen(),
            'lieu'=>$this->getLieu(),
            'place_libres'=>$this->getPlaces()-count($this->getUser())>0?$this->getPlaces()-count($this->getUser()):0,
            'inscrit'=>$inscrit
        ];
    }
    public function populatefromUser(){
        $inscrit = true;
        return [
            'id'=>$this->getId(),
            'date'=>$this->getDate(),
            'nom'=>$this->getNom(),
            'description'=>$this->getDescription(),
            'places'=>$this->getPlaces(),
            'is_open'=>$this->isIsOpen(),
            'lieu'=>$this->getLieu(),
            'place_libres'=>$this->getPlaces()-count($this->getUser())>0?$this->getPlaces()-count($this->getUser()):0,
            'inscrit'=>$inscrit
        ];
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }
}
