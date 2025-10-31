<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;

/**
 * @internal
 */
#[CoversClass(DeliveryReturnOrder::class)]
final class DeliveryReturnOrderTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new DeliveryReturnOrder();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'bizAddress' => ['bizAddress', ['key' => 'value']],
            'userAddress' => ['userAddress', ['key' => 'value']],
            'orderPrice' => ['orderPrice', 123],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 父类方法实现，当前测试类不需要特殊设置
    }

    public function testCreateEmptyEntity(): void
    {
        $entity = new DeliveryReturnOrder();
        $this->assertInstanceOf(DeliveryReturnOrder::class, $entity);
        $this->assertNull($entity->getId());
        $this->assertNull($entity->getCreateTime());
        $this->assertNull($entity->getUpdateTime());
    }

    public function testSetAndGetProperties(): void
    {
        $entity = new DeliveryReturnOrder();

        // Account 关联字段已被注释掉，暂时跳过这个测试
        // $account = $this->createMock(Account::class);
        // $this->assertSame($entity, $entity->setAccount($account));
        // $this->assertSame($account, $entity->getAccount());

        // 测试基本属性
        $entity->setShopOrderId('TEST-ORDER-123');
        $this->assertSame('TEST-ORDER-123', $entity->getShopOrderId());

        $entity->setOpenId('test-open-id');
        $this->assertSame('test-open-id', $entity->getOpenId());

        $entity->setOrderPath('pages/order/detail');
        $this->assertSame('pages/order/detail', $entity->getOrderPath());

        $entity->setOrderPrice(10000);
        $this->assertSame(10000, $entity->getOrderPrice());

        // 测试数组属性
        $bizAddress = [
            'name' => '商家',
            'mobile' => '13800138000',
            'province' => '北京市',
            'city' => '北京市',
            'area' => '海淀区',
            'address' => '详细地址',
        ];

        $entity->setBizAddress($bizAddress);
        $this->assertSame($bizAddress, $entity->getBizAddress());

        $userAddress = [
            'name' => '用户',
            'mobile' => '13900139000',
            'province' => '上海市',
            'city' => '上海市',
            'area' => '浦东新区',
            'address' => '详细地址',
        ];

        $entity->setUserAddress($userAddress);
        $this->assertSame($userAddress, $entity->getUserAddress());

        $goodsList = [
            [
                'name' => '测试商品',
                'url' => 'https://example.com/image.jpg',
            ],
        ];

        $entity->setGoodsList($goodsList);
        $this->assertSame($goodsList, $entity->getGoodsList());

        // 测试枚举类型
        $entity->setStatus(DeliveryReturnStatus::Appointment);
        $this->assertSame(DeliveryReturnStatus::Appointment, $entity->getStatus());

        $entity->setOrderStatus(DeliveryReturnOrderStatus::InTransit);
        $this->assertSame(DeliveryReturnOrderStatus::InTransit, $entity->getOrderStatus());

        // 测试其他字符串属性
        $entity->setReturnId('return-id-123');
        $this->assertSame('return-id-123', $entity->getReturnId());

        $entity->setWaybillId('waybill-123');
        $this->assertSame('waybill-123', $entity->getWaybillId());

        $entity->setDeliveryName('顺丰快递');
        $this->assertSame('顺丰快递', $entity->getDeliveryName());

        $entity->setDeliveryId('SF');
        $this->assertSame('SF', $entity->getDeliveryId());
    }

    public function testCreateTime(): void
    {
        $entity = new DeliveryReturnOrder();
        $now = new \DateTimeImmutable();

        $entity->setCreateTime($now);
        $this->assertSame($now, $entity->getCreateTime());
    }

    public function testUpdateTime(): void
    {
        $entity = new DeliveryReturnOrder();
        $now = new \DateTimeImmutable();

        $entity->setUpdateTime($now);
        $this->assertSame($now, $entity->getUpdateTime());
    }

    public function testRetrieveApiArray(): void
    {
        $entity = new DeliveryReturnOrder();
        $entity->setShopOrderId('TEST-ORDER-123');
        $entity->setOpenId('test-open-id');
        $entity->setOrderPath('pages/order/detail');
        $entity->setOrderPrice(10000);
        $entity->setReturnId('return-id-123');
        $entity->setWaybillId('waybill-123');
        $entity->setStatus(DeliveryReturnStatus::Appointment);
        $entity->setOrderStatus(DeliveryReturnOrderStatus::InTransit);
        $entity->setDeliveryName('顺丰快递');
        $entity->setDeliveryId('SF');

        $bizAddress = [
            'name' => '商家',
            'mobile' => '13800138000',
            'province' => '北京市',
            'city' => '北京市',
            'area' => '海淀区',
            'address' => '详细地址',
        ];
        $entity->setBizAddress($bizAddress);

        $userAddress = [
            'name' => '用户',
            'mobile' => '13900139000',
            'province' => '上海市',
            'city' => '上海市',
            'area' => '浦东新区',
            'address' => '详细地址',
        ];
        $entity->setUserAddress($userAddress);

        $apiArray = $entity->retrieveApiArray();
        $this->assertArrayHasKey('shopOrderId', $apiArray);
        $this->assertArrayHasKey('bizAddress', $apiArray);
        $this->assertArrayHasKey('userAddress', $apiArray);
        $this->assertArrayHasKey('returnId', $apiArray);
        $this->assertArrayHasKey('deliveryName', $apiArray);
        $this->assertArrayHasKey('deliveryId', $apiArray);

        $this->assertSame('TEST-ORDER-123', $apiArray['shopOrderId']);
        $this->assertSame('顺丰快递', $apiArray['deliveryName']);
        $this->assertSame('SF', $apiArray['deliveryId']);
        $this->assertSame($bizAddress, $apiArray['bizAddress']);
        $this->assertSame($userAddress, $apiArray['userAddress']);
    }
}
