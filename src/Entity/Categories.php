<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private array $content = [];

    #[ORM\OneToMany(mappedBy: 'id_categorie', targetEntity: Articles::class, orphanRemoval: true)]
    private Collection $articles;

    #[ORM\Column(nullable: true)]
    private ?array $description = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }



    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns an array representation of the entity.
     *
     * @return array
     */
    public function populate(): array
    {
        $articlesData = [];
        $articles = $this->getArticles();
        foreach ($articles as $article) {
            $articlesData[] = [
                'id'=>$article->getId(),
                'title'=>$article->getTitle(),
                'description'=>$article->getDescription(),
                'is_publish'=>$article->isIsPublish(),
                'contenu'=>$article->getContenu(),
            ];
        }
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description'=>$this->getDescription(),
            'content'=>$this->getContent(),
            'articles'=>$articlesData,
        ];
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
            $article->setIdCategorie($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getIdCategorie() === $this) {
                $article->setIdCategorie(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(?array $description): static
    {
        $this->description = $description;

        return $this;
    }
}
