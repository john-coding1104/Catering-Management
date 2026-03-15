<?php

namespace App\Entity;

use App\Repository\ServiceInventoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceInventoryRepository::class)]
#[ORM\Table(name: 'service_inventory')]
class ServiceInventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Services::class, inversedBy: 'serviceInventories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Services $service = null;

    #[ORM\ManyToOne(targetEntity: Inventory::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Inventory $inventory = null;

    #[ORM\Column]
    private ?float $quantityUsed = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $addedAt = null;

    public function __construct()
    {
        $this->addedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): static
    {
        $this->inventory = $inventory;
        return $this;
    }

    public function getQuantityUsed(): ?float
    {
        return $this->quantityUsed;
    }

    public function setQuantityUsed(float $quantityUsed): static
    {
        $this->quantityUsed = $quantityUsed;
        return $this;
    }

    public function getAddedAt(): ?\DateTime
    {
        return $this->addedAt;
    }

    public function setAddedAt(?\DateTime $addedAt): static
    {
        $this->addedAt = $addedAt;
        return $this;
    }
}
