<?php

namespace App\EventListener;

use App\Entity\ActivityLog;
use App\Entity\User;
use App\Service\ActivityLogService;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
class DoctrineEventListener
{
    // Entities to log
    private const TRACKED_ENTITIES = [
        'App\Entity\Booking',
        'App\Entity\Services',
        'App\Entity\Product',
        'App\Entity\Supplier',
        'App\Entity\Inventory',
    ];

    // Entities with manual logging in controllers (to avoid double logging)
    private const MANUAL_LOGGED_ENTITIES = [
        'App\Entity\Booking',
        'App\Entity\Inventory',
    ];

    public function __construct(
        private ActivityLogService $activityLogService,
        private Security $security
    ) {}

    /**
     * Log creation operations
     * Note: Booking and Inventory are logged manually in controllers with richer context
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        
        if (!$this->shouldTrack($entity) || $this->isManuallyLogged($entity)) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $recordType = $this->getEntityType($entity);
        $recordId = method_exists($entity, 'getId') ? $entity->getId() : null;
        $recordName = $this->getEntityName($entity);

        $this->activityLogService->logRecordCreation($user, $recordType, $recordId, $recordName);
    }

    /**
     * Log update operations
     * Note: Booking and Inventory are logged manually in controllers with richer context
     */
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        
        if (!$this->shouldTrack($entity) || $this->isManuallyLogged($entity)) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $recordType = $this->getEntityType($entity);
        $recordId = method_exists($entity, 'getId') ? $entity->getId() : null;
        $recordName = $this->getEntityName($entity);
        $uow = $args->getObjectManager()->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($entity);

        $this->activityLogService->logRecordUpdate($user, $recordType, $recordId, $recordName, $changes);
    }

    /**
     * Log deletion operations (for all tracked entities)
     */
    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();
        
        if (!$this->shouldTrack($entity)) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $recordType = $this->getEntityType($entity);
        $recordId = method_exists($entity, 'getId') ? $entity->getId() : null;
        $recordName = $this->getEntityName($entity);

        $this->activityLogService->logRecordDeletion($user, $recordType, $recordId, $recordName);
    }

    private function shouldTrack($entity): bool
    {
        foreach (self::TRACKED_ENTITIES as $trackedClass) {
            if ($entity instanceof $trackedClass) {
                return true;
            }
        }
        return false;
    }

    private function isManuallyLogged($entity): bool
    {
        foreach (self::MANUAL_LOGGED_ENTITIES as $trackedClass) {
            if ($entity instanceof $trackedClass) {
                return true;
            }
        }
        return false;
    }

    private function getEntityType($entity): string
    {
        $className = (new \ReflectionClass($entity))->getShortName();
        return match($className) {
            'Services' => 'Service',
            default => $className,
        };
    }

    private function getEntityName($entity): ?string
    {
        return match(true) {
            method_exists($entity, 'getName') => $entity->getName(),
            method_exists($entity, 'getCustomerName') => $entity->getCustomerName(),
            method_exists($entity, 'getTitle') => $entity->getTitle(),
            method_exists($entity, 'getUsername') => $entity->getUsername(),
            default => null,
        };
    }
}
