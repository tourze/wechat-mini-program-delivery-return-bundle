<?php

namespace WechatMiniProgramDeliveryReturnBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;

/**
 * @extends ServiceEntityRepository<DeliveryReturnOrder>
 */
#[AsRepository(entityClass: DeliveryReturnOrder::class)]
class DeliveryReturnOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryReturnOrder::class);
    }

    public function save(DeliveryReturnOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DeliveryReturnOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
