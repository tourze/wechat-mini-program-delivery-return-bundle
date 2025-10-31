<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;

/**
 * @internal
 */
#[CoversClass(DeliveryReturnOrderStatus::class)]
final class DeliveryReturnOrderStatusTest extends AbstractEnumTestCase
{
    #[TestWith([100])]
    #[TestWith([-1])]
    #[TestWith([999])]
    public function testTryFromInvalidValue(int $invalidValue): void
    {
        $this->assertNull(DeliveryReturnOrderStatus::tryFrom($invalidValue));
    }

    public function testValueUniqueness(): void
    {
        $values = [];
        foreach (DeliveryReturnOrderStatus::cases() as $case) {
            $this->assertNotContains($case->value, $values, 'Duplicate value found: ' . $case->value);
            $values[] = $case->value;
        }
    }

    public function testLabelUniqueness(): void
    {
        $labels = [];
        foreach (DeliveryReturnOrderStatus::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotContains($label, $labels, 'Duplicate label found: ' . $label);
            $labels[] = $label;
        }
    }

    public function testToArray(): void
    {
        $enum = DeliveryReturnOrderStatus::Ordered;
        $array = $enum->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertSame(0, $array['value']);
        $this->assertSame('已下单待揽件', $array['label']);

        $enum2 = DeliveryReturnOrderStatus::Unknown;
        $array2 = $enum2->toArray();
        $this->assertIsArray($array2);
        $this->assertCount(2, $array2);
        $this->assertSame(99, $array2['value']);
        $this->assertSame('未知', $array2['label']);
    }
}
