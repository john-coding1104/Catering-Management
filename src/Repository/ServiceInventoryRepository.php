<?php

namespace App\Repository;

use App\Entity\ServiceInventory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceInventory>
 *
 * @method ServiceInventory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceInventory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceInventory[]    findAll()
 * @method ServiceInventory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceInventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceInventory::class);
    }
}
