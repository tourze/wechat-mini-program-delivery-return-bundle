<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;

class DeliveryReturnStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, DeliveryReturnStatus::Waiting->value);
        $this->assertSame(1, DeliveryReturnStatus::Appointment->value);
        $this->assertSame(2, DeliveryReturnStatus::Filled->value);
    }
    
    public function testGetLabel(): void
    {
        $this->assertSame('用户未填写退货信', DeliveryReturnStatus::Waiting->getLabel());
        $this->assertSame('预约上门取件', DeliveryReturnStatus::Appointment->getLabel());
        $this->assertSame('填写自行寄回运单号', DeliveryReturnStatus::Filled->getLabel());
    }
    
    public function testTryFromValidValue(): void
    {
        $this->assertSame(DeliveryReturnStatus::Waiting, DeliveryReturnStatus::tryFrom(0));
        $this->assertSame(DeliveryReturnStatus::Appointment, DeliveryReturnStatus::tryFrom(1));
        $this->assertSame(DeliveryReturnStatus::Filled, DeliveryReturnStatus::tryFrom(2));
    }
    
    public function testTryFromInvalidValue(): void
    {
        $this->assertNull(DeliveryReturnStatus::tryFrom(99));
        $this->assertNull(DeliveryReturnStatus::tryFrom(-1));
        // 不测试null值，因为会产生废弃警告
    }
} 