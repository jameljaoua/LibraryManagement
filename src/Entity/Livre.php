<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?int $qte = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(length: 20)]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $datepub = null;

    /**
     * @var Collection<int, Auteur>
     */
    #[ORM\ManyToMany(targetEntity: Auteur::class, inversedBy: 'livres')]
   #[ORM\JoinTable(name: 'livre_auteur')]

    private Collection $auteurs;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Editeur $editeur = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'livre')]
    private Collection $cartItems;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'livre')]
    private Collection $orderItems;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getDatepub(): ?\DateTime
    {
        return $this->datepub;
    }

    public function setDatepub(\DateTime $datepub): static
    {
        $this->datepub = $datepub;

        return $this;
    }

    /**
     * @return Collection<int, Auteur>
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): static
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs->add($auteur);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        $this->auteurs->removeElement($auteur);

        return $this;
    }
    public function getEditeur(): ?Editeur
    {
        return $this->editeur;
    }

    public function setEditeur(?Editeur $editeur): static
     {
        $this->editeur = $editeur;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }
        public function __toString(): string
    {
        return $this->titre;
    }

        /**
         * @return Collection<int, CartItem>
         */
        public function getCartItems(): Collection
        {
            return $this->cartItems;
        }

        public function addCartItem(CartItem $cartItem): static
        {
            if (!$this->cartItems->contains($cartItem)) {
                $this->cartItems->add($cartItem);
                $cartItem->setLivre($this);
            }

            return $this;
        }

        public function removeCartItem(CartItem $cartItem): static
        {
            if ($this->cartItems->removeElement($cartItem)) {
                // set the owning side to null (unless already changed)
                if ($cartItem->getLivre() === $this) {
                    $cartItem->setLivre(null);
                }
            }

            return $this;
        }

        /**
         * @return Collection<int, OrderItem>
         */
        public function getOrderItems(): Collection
        {
            return $this->orderItems;
        }

        public function addOrderItem(OrderItem $orderItem): static
        {
            if (!$this->orderItems->contains($orderItem)) {
                $this->orderItems->add($orderItem);
                $orderItem->setLivre($this);
            }

            return $this;
        }

        public function removeOrderItem(OrderItem $orderItem): static
        {
            if ($this->orderItems->removeElement($orderItem)) {
                // set the owning side to null (unless already changed)
                if ($orderItem->getLivre() === $this) {
                    $orderItem->setLivre(null);
                }
            }

            return $this;
        }
}
