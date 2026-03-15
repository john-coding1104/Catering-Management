<?php

namespace App\Entity;

use App\Repository\BookingInventoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingInventoryRepository::class)]
#[ORM\Table(name: 'booking_inventory')]
class BookingInventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Booking::class, inversedBy: 'bookingInventories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Booking $booking = null;

    #[ORM\ManyToOne(targetEntity: Inventory::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Inventory $inventory = null;

    #[ORM\Column(type: 'float', name: 'quantity_used')]
    private ?float $quantityUsed = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $addedAt = null;

    public function __construct()
    {
        $this->addedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getBooking(): ?Booking { return $this->booking; }
    public function setBooking(?Booking $booking): static { $this->booking = $booking; return $this; }
    public function getInventory(): ?Inventory { return $this->inventory; }
    public function setInventory(?Inventory $inventory): static { $this->inventory = $inventory; return $this; }
    public function getQuantityUsed(): ?float { return $this->quantityUsed; }
    public function setQuantityUsed(float $quantityUsed): static { $this->quantityUsed = $quantityUsed; return $this; }
    public function getAddedAt(): ?\DateTimeInterface { return $this->addedAt; }
    public function setAddedAt(?\DateTimeInterface $addedAt): static { $this->addedAt = $addedAt; return $this; }
}