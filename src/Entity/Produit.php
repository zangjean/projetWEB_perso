<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use http\Message;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Table(name: 'l3_produits')]
#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'libellé du produit'])]
    private ?int $id = null;

    #[ORM\Column(
        name: 'prix_unitaire',
        type: Types::INTEGER,
        nullable: false,
        options: ['comment' => 'en euro avec un nombre reel']
    )]
    #[Assert\Positive(message: 'Le prix unitaire doit etre positif')]
    private ?int $prixUnitaire = null;


    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: false)
    ]
    private ?string $nom = null;

    #[ORM\Column(
        name: 'quantite_en_stock',
        type: Types::INTEGER,
        nullable: false
    )]
    #[Assert\Positive(message: 'La quantité en stock doit etre positif')]
    private ?int $quantiteEnStock = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    private ?string $description = null;

    /**
     * @var Collection<int, Pays>
     */
    #[ORM\ManyToMany(targetEntity: Pays::class,inversedBy: 'produits')]
    #[ORM\JoinTable(name: 'l3_produits_pays')]
    #[ORM\JoinColumn(name: 'id_produit', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'id_pays', referencedColumnName: 'id', nullable: false)]
    private Collection $payss;

    public function __construct()
    {
        $this->description = null;
        $this->payss = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixUnitaire(): ?int
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(int $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

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

    public function getQuantiteEnStock(): ?int
    {
        return $this->quantiteEnStock;
    }

    public function setQuantiteEnStock(int $quantiteEnStock): static
    {
        $this->quantiteEnStock = $quantiteEnStock;

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

    /**
     * @return Collection<int, Pays>
     */
    public function getPayss(): Collection
    {
        return $this->payss;
    }

    public function addPays(Pays $pays): static
    {
        if (!$this->payss->contains($pays)) {
            $this->payss->add($pays);
        }
        return $this;
    }

    public function removePays(Pays $pays): static
    {
        $this->payss->removeElement($pays);

        return $this;
    }
}
