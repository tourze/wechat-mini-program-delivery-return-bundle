<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;

class DeliveryReturnOrderStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, DeliveryReturnOrderStatus::Ordered->value);
        $this->assertSame(1, DeliveryReturnOrderStatus::PickedUp->value);
        $this->assertSame(2, DeliveryReturnOrderStatus::InTransit->value);
        $this->assertSame(3, DeliveryReturnOrderStatus::OutForDelivery->value);
        $this->assertSame(4, DeliveryReturnOrderStatus::Delivered->value);
        $this->assertSame(5, DeliveryReturnOrderStatus::Exception->value);
        $this->assertSame(6, DeliveryReturnOrderStatus::ProxyDelivered->value);
        $this->assertSame(7, DeliveryReturnOrderStatus::PickUpFailed->value);
        $this->assertSame(8, DeliveryReturnOrderStatus::DeliveryFailed->value);
        $this->assertSame(11, DeliveryReturnOrderStatus::Cancelled->value);
        $this->assertSame(13, DeliveryReturnOrderStatus::Returning->value);
        $this->assertSame(14, DeliveryReturnOrderStatus::Returned->value);
        $this->assertSame(99, DeliveryReturnOrderStatus::Unknown->value);
    }
    
    public function testGetLabel(): void
    {
        $this->assertSame('已下单待揽件', DeliveryReturnOrderStatus::Ordered->getLabel());
        $this->assertSame('已揽件', DeliveryReturnOrderStatus::PickedUp->getLabel());
        $this->assertSame('运输中', DeliveryReturnOrderStatus::InTransit->getLabel());
        $this->assertSame('派件中', DeliveryReturnOrderStatus::OutForDelivery->getLabel());
        $this->assertSame('已签收', DeliveryReturnOrderStatus::Delivered->getLabel());
        $this->assertSame('异常', DeliveryReturnOrderStatus::Exception->getLabel());
        $this->assertSame('代签收', DeliveryReturnOrderStatus::ProxyDelivered->getLabel());
        $this->assertSame('揽收失败', DeliveryReturnOrderStatus::PickUpFailed->getLabel());
        $this->assertSame('签收失败（拒收，超区）', DeliveryReturnOrderStatus::DeliveryFailed->getLabel());
        $this->assertSame('已取消', DeliveryReturnOrderStatus::Cancelled->getLabel());
        $this->assertSame('退件中', DeliveryReturnOrderStatus::Returning->getLabel());
        $this->assertSame('已退件', DeliveryReturnOrderStatus::Returned->getLabel());
        $this->assertSame('未知', DeliveryReturnOrderStatus::Unknown->getLabel());
    }
    
    public function testTryFromValidValue(): void
    {
        $this->assertSame(DeliveryReturnOrderStatus::Ordered, DeliveryReturnOrderStatus::tryFrom(0));
        $this->assertSame(DeliveryReturnOrderStatus::PickedUp, DeliveryReturnOrderStatus::tryFrom(1));
        $this->assertSame(DeliveryReturnOrderStatus::InTransit, DeliveryReturnOrderStatus::tryFrom(2));
        $this->assertSame(DeliveryReturnOrderStatus::OutForDelivery, DeliveryReturnOrderStatus::tryFrom(3));
        $this->assertSame(DeliveryReturnOrderStatus::Delivered, DeliveryReturnOrderStatus::tryFrom(4));
        $this->assertSame(DeliveryReturnOrderStatus::Exception, DeliveryReturnOrderStatus::tryFrom(5));
        $this->assertSame(DeliveryReturnOrderStatus::ProxyDelivered, DeliveryReturnOrderStatus::tryFrom(6));
        $this->assertSame(DeliveryReturnOrderStatus::PickUpFailed, DeliveryReturnOrderStatus::tryFrom(7));
        $this->assertSame(DeliveryReturnOrderStatus::DeliveryFailed, DeliveryReturnOrderStatus::tryFrom(8));
        $this->assertSame(DeliveryReturnOrderStatus::Cancelled, DeliveryReturnOrderStatus::tryFrom(11));
        $this->assertSame(DeliveryReturnOrderStatus::Returning, DeliveryReturnOrderStatus::tryFrom(13));
        $this->assertSame(DeliveryReturnOrderStatus::Returned, DeliveryReturnOrderStatus::tryFrom(14));
        $this->assertSame(DeliveryReturnOrderStatus::Unknown, DeliveryReturnOrderStatus::tryFrom(99));
    }
    
    public function testTryFromInvalidValue(): void
    {
        $this->assertNull(DeliveryReturnOrderStatus::tryFrom(100));
        $this->assertNull(DeliveryReturnOrderStatus::tryFrom(-1));
        // 不测试null值，因为会产生废弃警告
    }
} 