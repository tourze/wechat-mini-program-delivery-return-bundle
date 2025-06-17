<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;

class DeliveryReturnOrderTest extends TestCase
{
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
        
        // 测试Account关联
        $account = $this->createMock(Account::class);
        $this->assertSame($entity, $entity->setAccount($account));
        $this->assertSame($account, $entity->getAccount());
        
        // 测试基本属性
        $this->assertSame($entity, $entity->setShopOrderId('TEST-ORDER-123'));
        $this->assertSame('TEST-ORDER-123', $entity->getShopOrderId());
        
        $this->assertSame($entity, $entity->setOpenId('test-open-id'));
        $this->assertSame('test-open-id', $entity->getOpenId());
        
        $this->assertSame($entity, $entity->setOrderPath('pages/order/detail'));
        $this->assertSame('pages/order/detail', $entity->getOrderPath());
        
        $this->assertSame($entity, $entity->setOrderPrice(10000));
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
        
        $this->assertSame($entity, $entity->setBizAddress($bizAddress));
        $this->assertSame($bizAddress, $entity->getBizAddress());
        
        $userAddress = [
            'name' => '用户',
            'mobile' => '13900139000',
            'province' => '上海市',
            'city' => '上海市',
            'area' => '浦东新区',
            'address' => '详细地址',
        ];
        
        $this->assertSame($entity, $entity->setUserAddress($userAddress));
        $this->assertSame($userAddress, $entity->getUserAddress());
        
        $goodsList = [
            [
                'name' => '测试商品',
                'url' => 'https://example.com/image.jpg'
            ]
        ];
        
        $this->assertSame($entity, $entity->setGoodsList($goodsList));
        $this->assertSame($goodsList, $entity->getGoodsList());
        
        // 测试枚举类型
        $this->assertSame($entity, $entity->setStatus(DeliveryReturnStatus::Appointment));
        $this->assertSame(DeliveryReturnStatus::Appointment, $entity->getStatus());
        
        $this->assertSame($entity, $entity->setOrderStatus(DeliveryReturnOrderStatus::InTransit));
        $this->assertSame(DeliveryReturnOrderStatus::InTransit, $entity->getOrderStatus());
        
        // 测试其他字符串属性
        $this->assertSame($entity, $entity->setReturnId('return-id-123'));
        $this->assertSame('return-id-123', $entity->getReturnId());
        
        $this->assertSame($entity, $entity->setWaybillId('waybill-123'));
        $this->assertSame('waybill-123', $entity->getWaybillId());
        
        $this->assertSame($entity, $entity->setDeliveryName('顺丰快递'));
        $this->assertSame('顺丰快递', $entity->getDeliveryName());
        
        $this->assertSame($entity, $entity->setDeliveryId('SF'));
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
        
        $this->assertIsArray($apiArray);
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