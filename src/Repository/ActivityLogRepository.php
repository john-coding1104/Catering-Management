<?php

namespace App\Repository;

use App\Entity\ActivityLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivityLog>
 */
class ActivityLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityLog::class);
    }

    /**
     * Find logs by user ID
     */
    public function findByUserId(int $userId, int $limit = 100): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find logs by action
     */
    public function findByAction(string $action, int $limit = 100): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.action = :action')
            ->setParameter('action', $action)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find logs within a date range
     */
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate, int $limit = 100): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.createdAt >= :startDate')
            ->andWhere('a.createdAt <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find logs by user and action
     */
    public function findByUserAndAction(int $userId, string $action, int $limit = 100): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :userId')
            ->andWhere('a.action = :action')
            ->setParameter('userId', $userId)
            ->setParameter('action', $action)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all logs with pagination and filtering
     */
    public function findWithFilters(
        ?int $userId = null,
        ?string $action = null,
        ?\DateTime $startDate = null,
        ?\DateTime $endDate = null,
        int $page = 1,
        int $limit = 50
    ): Paginator
    {
        $qb = $this->createQueryBuilder('a');

        if ($userId) {
            $qb->andWhere('a.user = :userId')
                ->setParameter('userId', $userId);
        }

        if ($action) {
            $qb->andWhere('a.action = :action')
                ->setParameter('action', $action);
        }

        if ($startDate) {
            $qb->andWhere('a.createdAt >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $qb->andWhere('a.createdAt <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        $qb->orderBy('a.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($qb->getQuery(), false);
    }

    /**
     * Get recent logs (last N records)
     */
    public function getRecentLogs(int $limit = 50): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get logs by role
     */
    public function findByRole(string $role, int $limit = 100): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.role = :role')
            ->setParameter('role', $role)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get distinct actions
     */
    public function findDistinctActions(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.action')
            ->orderBy('a.action', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get distinct record types
     */
    public function findDistinctRecordTypes(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.recordType')
            ->where('a.recordType IS NOT NULL')
            ->orderBy('a.recordType', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get distinct roles
     */
    public function findDistinctRoles(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.role')
            ->orderBy('a.role', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get distinct usernames
     */
    public function findDistinctUsernames(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.username')
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find logs by record type and ID
     */
    public function findByRecord(string $recordType, int $recordId, int $limit = 100): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.recordType = :recordType')
            ->andWhere('a.recordId = :recordId')
            ->setParameter('recordType', $recordType)
            ->setParameter('recordId', $recordId)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
