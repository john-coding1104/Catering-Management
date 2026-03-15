<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\BookingInventory;
use App\Entity\Inventory;
use App\Entity\Services;
use App\Entity\ServiceInventory;
use Doctrine\ORM\EntityManagerInterface;

class InventoryStockManager
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Deduct inventory stock when a service is created or updated
     *
     * @param Services $service
     * @param array $inventoryData Array of ['inventory_id' => quantity_used, ...]
     * @throws \Exception If not enough stock available
     */
    public function deductStockForService(Services $service, array $inventoryData): void
    {
        // First, validate that all inventory items have sufficient stock
        foreach ($inventoryData as $inventoryId => $quantityUsed) {
            $inventory = $this->entityManager->getRepository(Inventory::class)->find($inventoryId);
            
            if (!$inventory) {
                throw new \Exception("Inventory item with ID {$inventoryId} not found.");
            }

            if ($quantityUsed <= 0) {
                continue; // Skip zero or negative quantities
            }

            if ($inventory->getCurrentStock() < $quantityUsed) {
                throw new \Exception(
                    "Insufficient stock for '{$inventory->getName()}'. " .
                    "Available: {$inventory->getCurrentStock()}, Required: {$quantityUsed}"
                );
            }
        }

        // If validation passes, deduct the stock
        foreach ($inventoryData as $inventoryId => $quantityUsed) {
            if ($quantityUsed <= 0) {
                continue;
            }

            $inventory = $this->entityManager->getRepository(Inventory::class)->find($inventoryId);
            
            // Deduct stock
            $newStock = $inventory->getCurrentStock() - $quantityUsed;
            $inventory->setCurrentStock($newStock);
            $inventory->setUpdatedAt(new \DateTime());

            // Create ServiceInventory record to track usage
            $serviceInventory = new ServiceInventory();
            $serviceInventory->setService($service);
            $serviceInventory->setInventory($inventory);
            $serviceInventory->setQuantityUsed($quantityUsed);
            
            $service->addServiceInventory($serviceInventory);
            $this->entityManager->persist($serviceInventory);
        }

        // Update service timestamp
        $service->setUpdatedAt(new \DateTime());
    }

    /**
     * Restore inventory stock when a service is deleted
     *
     * @param Services $service
     */
    public function restoreStockForService(Services $service): void
    {
        foreach ($service->getServiceInventories() as $serviceInventory) {
            $inventory = $serviceInventory->getInventory();
            $quantityUsed = $serviceInventory->getQuantityUsed();

            // Restore stock
            $newStock = $inventory->getCurrentStock() + $quantityUsed;
            $inventory->setCurrentStock($newStock);
            $inventory->setUpdatedAt(new \DateTime());
            
            $this->entityManager->persist($inventory);
        }

        $service->setUpdatedAt(new \DateTime());
    }

    /**
     * Deduct inventory stock when a booking is created or updated.
     *
     * @param Booking $booking
     * @param array   $inventoryData Array of ['inventory_id' => quantity_used, ...]
     *
     * @throws \Exception If not enough stock available
     */
    public function deductStockForBooking(Booking $booking, array $inventoryData): void
    {
        foreach ($inventoryData as $inventoryId => $quantityUsed) {
            $inventory = $this->entityManager->getRepository(Inventory::class)->find($inventoryId);

            if (!$inventory) {
                throw new \Exception("Inventory item with ID {$inventoryId} not found.");
            }

            if ($quantityUsed <= 0) {
                continue;
            }

            if ($inventory->getCurrentStock() < $quantityUsed) {
                throw new \Exception(
                    "Insufficient stock for '{$inventory->getName()}'. " .
                    "Available: {$inventory->getCurrentStock()}, Required: {$quantityUsed}"
                );
            }
        }

        foreach ($inventoryData as $inventoryId => $quantityUsed) {
            if ($quantityUsed <= 0) {
                continue;
            }

            $inventory = $this->entityManager->getRepository(Inventory::class)->find($inventoryId);

            $newStock = $inventory->getCurrentStock() - $quantityUsed;
            $inventory->setCurrentStock($newStock);
            $inventory->setUpdatedAt(new \DateTime());

            $bookingInventory = new BookingInventory();
            $bookingInventory->setBooking($booking);
            $bookingInventory->setInventory($inventory);
            $bookingInventory->setQuantityUsed($quantityUsed);

            $booking->addBookingInventory($bookingInventory);
            $this->entityManager->persist($bookingInventory);
        }
    }

    /**
     * Restore inventory stock when a booking is modified or deleted.
     *
     * @param Booking $booking
     */
    public function restoreStockForBooking(Booking $booking): void
    {
        foreach ($booking->getBookingInventories() as $bookingInventory) {
            $inventory = $bookingInventory->getInventory();
            $quantityUsed = $bookingInventory->getQuantityUsed();

            $inventory->setCurrentStock($inventory->getCurrentStock() + $quantityUsed);
            $inventory->setUpdatedAt(new \DateTime());

            $this->entityManager->remove($bookingInventory);
        }
    }

    /**
     * Get total stock usage for a service
     *
     * @param Services $service
     * @return float
     */
    public function getTotalStockUsage(Services $service): float
    {
        $total = 0;
        foreach ($service->getServiceInventories() as $serviceInventory) {
            $total += $serviceInventory->getQuantityUsed();
        }
        return $total;
    }
}
