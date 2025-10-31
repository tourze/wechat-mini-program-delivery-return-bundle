<?php

declare(strict_types=1);

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;
use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;

/**
 * @internal
 */
#[CoversClass(DeliveryReturnOrderRepository::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryReturnOrderRepositoryTest extends AbstractRepositoryTestCase
{
    private DeliveryReturnOrderRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(DeliveryReturnOrderRepository::class);

        // 不需要清空数据，因为父类的 setUp() 已经执行了 cleanDatabase()
        // 这会更新 schema 并加载所有 DataFixtures，包括 DeliveryReturnOrderFixtures
    }

    /**
     * 清空 DataFixtures 数据的辅助方法，用于需要精确控制数据数量的测试
     */
    private function clearFixtureData(): void
    {
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . DeliveryReturnOrder::class)->execute();
        $entityManager->flush();
    }

    public function testFindByWithEmptyCriteriaShouldReturnAllEntities(): void
    {
        $this->clearFixtureData();

        $entity = $this->createDeliveryReturnOrder();
        $this->persistAndFlush($entity);

        $result = $this->repository->findBy([]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function testFindByWithMatchingCriteriaShouldReturnCorrectEntities(): void
    {
        $this->clearFixtureData();

        $entity = $this->createDeliveryReturnOrder();
        $this->persistAndFlush($entity);

        $result = $this->repository->findBy(['id' => $entity->getId()]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($entity->getId(), $result[0]->getId());
    }

    public function testSaveShouldPersistEntity(): void
    {
        $entity = $this->createDeliveryReturnOrder();

        $this->repository->save($entity);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $entity = $this->createDeliveryReturnOrder();
        $this->persistAndFlush($entity);
        $entityId = $entity->getId();

        $this->repository->remove($entity);

        $this->assertEntityNotExists(DeliveryReturnOrder::class, $entityId);
        $this->assertNotNull($entityId);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $entity = $this->createDeliveryReturnOrder();
        $this->persistAndFlush($entity);

        $this->repository->remove($entity, false);

        $em = self::getEntityManager();
        $em->clear();
        $found = $em->find(DeliveryReturnOrder::class, $entity->getId());
        $this->assertNotNull($found);
    }

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        $entity1 = $this->createDeliveryReturnOrder();
        $entity2 = $this->createDeliveryReturnOrder();
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy([], ['id' => 'DESC']);

        $this->assertNotNull($result);
        $this->assertSame($entity2->getId(), $result->getId());
    }

    public function testFindByNullableOrderPathFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setOrderPath('/pages/order/detail');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setOrderPath(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithValue = $this->repository->findBy(['orderPath' => '/pages/order/detail']);
        $resultWithNull = $this->repository->findBy(['orderPath' => null]);

        $this->assertCount(1, $resultWithValue);
        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity1->getId(), $resultWithValue[0]->getId());
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    public function testFindByNullableGoodsListFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setGoodsList([['name' => 'item1'], ['name' => 'item2']]);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setGoodsList(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithNull = $this->repository->findBy(['goodsList' => null]);

        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    public function testCountByNullableReturnIdFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setReturnId('return123');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setReturnId(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithValue = $this->repository->count(['returnId' => 'return123']);
        $countWithNull = $this->repository->count(['returnId' => null]);

        $this->assertSame(1, $countWithValue);
        $this->assertSame(1, $countWithNull);
    }

    public function testCountByNullableWaybillIdFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setWaybillId('waybill123');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setWaybillId(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithValue = $this->repository->count(['waybillId' => 'waybill123']);
        $countWithNull = $this->repository->count(['waybillId' => null]);

        $this->assertSame(1, $countWithValue);
        $this->assertSame(1, $countWithNull);
    }

    public function testFindByNullableStatusFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setStatus(DeliveryReturnStatus::Filled);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setStatus(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithValue = $this->repository->findBy(['status' => DeliveryReturnStatus::Filled]);
        $resultWithNull = $this->repository->findBy(['status' => null]);

        $this->assertCount(1, $resultWithValue);
        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity1->getId(), $resultWithValue[0]->getId());
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    public function testCountByNullableStatusFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setStatus(DeliveryReturnStatus::Filled);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setStatus(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithValue = $this->repository->count(['status' => DeliveryReturnStatus::Filled]);
        $countWithNull = $this->repository->count(['status' => null]);

        $this->assertSame(1, $countWithValue);
        $this->assertSame(1, $countWithNull);
    }

    public function testFindByNullableOrderStatusFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setOrderStatus(DeliveryReturnOrderStatus::Ordered);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setOrderStatus(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithValue = $this->repository->findBy(['orderStatus' => DeliveryReturnOrderStatus::Ordered]);
        $resultWithNull = $this->repository->findBy(['orderStatus' => null]);

        $this->assertCount(1, $resultWithValue);
        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity1->getId(), $resultWithValue[0]->getId());
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    public function testCountByNullableOrderStatusFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setOrderStatus(DeliveryReturnOrderStatus::Ordered);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setOrderStatus(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithValue = $this->repository->count(['orderStatus' => DeliveryReturnOrderStatus::Ordered]);
        $countWithNull = $this->repository->count(['orderStatus' => null]);

        $this->assertSame(1, $countWithValue);
        $this->assertSame(1, $countWithNull);
    }

    public function testFindByNullableDeliveryNameFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setDeliveryName('顺丰速运');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setDeliveryName(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithValue = $this->repository->findBy(['deliveryName' => '顺丰速运']);
        $resultWithNull = $this->repository->findBy(['deliveryName' => null]);

        $this->assertCount(1, $resultWithValue);
        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity1->getId(), $resultWithValue[0]->getId());
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    public function testCountByNullableDeliveryIdFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setDeliveryId('SF01');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setDeliveryId(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithValue = $this->repository->count(['deliveryId' => 'SF01']);
        $countWithNull = $this->repository->count(['deliveryId' => null]);

        $this->assertSame(1, $countWithValue);
        $this->assertSame(1, $countWithNull);
    }

    public function testFindOneByWithMultipleOrderByCriteriaShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setOrderPrice(1000);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setOrderPrice(2000);
        $entity3 = $this->createDeliveryReturnOrder();
        $entity3->setOrderPrice(1500);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $resultAsc = $this->repository->findOneBy([], ['orderPrice' => 'ASC']);
        $resultDesc = $this->repository->findOneBy([], ['orderPrice' => 'DESC']);
        $resultIdDesc = $this->repository->findOneBy([], ['id' => 'DESC', 'orderPrice' => 'ASC']);

        $this->assertNotNull($resultAsc);
        $this->assertNotNull($resultDesc);
        $this->assertNotNull($resultIdDesc);
        $this->assertSame($entity1->getId(), $resultAsc->getId());
        $this->assertSame($entity2->getId(), $resultDesc->getId());
        $this->assertSame($entity3->getId(), $resultIdDesc->getId());
    }

    public function testFindOneByWithNullValueOrderingShouldWork(): void
    {
        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setReturnId('return1');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setReturnId(null);
        $entity3 = $this->createDeliveryReturnOrder();
        $entity3->setReturnId('return3');
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $resultAsc = $this->repository->findOneBy([], ['returnId' => 'ASC']);
        $resultDesc = $this->repository->findOneBy([], ['returnId' => 'DESC']);

        $this->assertNotNull($resultAsc);
        $this->assertNotNull($resultDesc);
    }

    public function testCountByNullableOrderPathFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setOrderPath('/pages/order/detail');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setOrderPath(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithValue = $this->repository->count(['orderPath' => '/pages/order/detail']);
        $countWithNull = $this->repository->count(['orderPath' => null]);

        $this->assertSame(1, $countWithValue);
        $this->assertSame(1, $countWithNull);
    }

    public function testFindByNullableGoodsListFieldWithValueShouldWork(): void
    {
        $goodsList = [['name' => 'item1'], ['name' => 'item2']];
        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setGoodsList($goodsList);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setGoodsList([['name' => 'item3'], ['name' => 'item4']]);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $allEntities = $this->repository->findAll();
        $foundEntity = null;
        foreach ($allEntities as $entity) {
            // Type assertion to ensure $entity is DeliveryReturnOrder
            self::assertInstanceOf(DeliveryReturnOrder::class, $entity);
            // Ensure getGoodsList() returns a non-null value before comparison
            if (null !== $entity->getGoodsList() && $entity->getGoodsList() === $goodsList) {
                $foundEntity = $entity;
                break;
            }
        }

        $this->assertNotNull($foundEntity);
        $this->assertSame($entity1->getId(), $foundEntity->getId());
    }

    public function testCountByNullableGoodsListFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setGoodsList([['name' => 'item1'], ['name' => 'item2']]);
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setGoodsList(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithNull = $this->repository->count(['goodsList' => null]);

        $this->assertSame(1, $countWithNull);
    }

    public function testFindByNullableReturnIdFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setReturnId('return123');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setReturnId(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithValue = $this->repository->findBy(['returnId' => 'return123']);
        $resultWithNull = $this->repository->findBy(['returnId' => null]);

        $this->assertCount(1, $resultWithValue);
        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity1->getId(), $resultWithValue[0]->getId());
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    public function testFindByNullableWaybillIdFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setWaybillId('waybill123');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setWaybillId(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithValue = $this->repository->findBy(['waybillId' => 'waybill123']);
        $resultWithNull = $this->repository->findBy(['waybillId' => null]);

        $this->assertCount(1, $resultWithValue);
        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity1->getId(), $resultWithValue[0]->getId());
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    public function testCountByNullableDeliveryNameFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setDeliveryName('顺丰速运');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setDeliveryName(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $countWithValue = $this->repository->count(['deliveryName' => '顺丰速运']);
        $countWithNull = $this->repository->count(['deliveryName' => null]);

        $this->assertSame(1, $countWithValue);
        $this->assertSame(1, $countWithNull);
    }

    public function testFindByNullableDeliveryIdFieldShouldWork(): void
    {
        $this->clearFixtureData();

        $entity1 = $this->createDeliveryReturnOrder();
        $entity1->setDeliveryId('SF01');
        $entity2 = $this->createDeliveryReturnOrder();
        $entity2->setDeliveryId(null);
        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $resultWithValue = $this->repository->findBy(['deliveryId' => 'SF01']);
        $resultWithNull = $this->repository->findBy(['deliveryId' => null]);

        $this->assertCount(1, $resultWithValue);
        $this->assertCount(1, $resultWithNull);
        $this->assertSame($entity1->getId(), $resultWithValue[0]->getId());
        $this->assertSame($entity2->getId(), $resultWithNull[0]->getId());
    }

    private function createDeliveryReturnOrder(): DeliveryReturnOrder
    {
        $entity = new DeliveryReturnOrder();
        $entity->setOpenId('test_openid_' . uniqid());
        $entity->setShopOrderId('test_shop_order_' . uniqid());
        $entity->setBizAddress([
            'name' => 'Test Biz',
            'mobile' => '123456789',
            'country' => '中国',
            'province' => '广东省',
            'city' => '深圳市',
            'area' => '南山区',
            'address' => '科技园路1号',
        ]);
        $entity->setUserAddress([
            'name' => 'Test User',
            'mobile' => '987654321',
            'country' => '中国',
            'province' => '广东省',
            'city' => '深圳市',
            'area' => '福田区',
            'address' => '华强北路2号',
        ]);
        $entity->setOrderPrice(10000);
        $entity->setStatus(DeliveryReturnStatus::Waiting);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $entity = new DeliveryReturnOrder();
        $entity->setOpenId('test_openid_' . uniqid());
        $entity->setShopOrderId('test_shop_order_' . uniqid());
        $entity->setBizAddress([
            'name' => 'Test Biz',
            'mobile' => '123456789',
            'city_name' => 'Test City',
            'county_name' => 'Test County',
            'detail_info' => 'Test Address',
            'province_name' => 'Test Province',
            'tel_number' => '123456789',
        ]);
        $entity->setUserAddress([
            'userName' => 'Test User',
            'telNumber' => '987654321',
            'provinceName' => 'User Province',
            'cityName' => 'User City',
            'countyName' => 'User County',
            'detailInfo' => 'User Address',
        ]);
        $entity->setOrderPrice(10000);
        $entity->setStatus(DeliveryReturnStatus::Waiting);

        return $entity;
    }

    /**
     * @return DeliveryReturnOrderRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
