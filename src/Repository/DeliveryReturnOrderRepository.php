<?php

namespace WechatMiniProgramDeliveryReturnBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;

/**
 * @method DeliveryReturnOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryReturnOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryReturnOrder[]    findAll()
 * @method DeliveryReturnOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryReturnOrderRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryReturnOrder::class);
    }
}
