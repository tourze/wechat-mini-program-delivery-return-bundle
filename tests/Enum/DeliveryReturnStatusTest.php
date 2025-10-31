<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;

/**
 * @internal
 */
#[CoversClass(DeliveryReturnStatus::class)]
final class DeliveryReturnStatusTest extends AbstractEnumTestCase
{
    #[TestWith([DeliveryReturnStatus::Waiting, 0])]
    #[TestWith([DeliveryReturnStatus::Appointment, 1])]
    #[TestWith([DeliveryReturnStatus::Filled, 2])]
    public function testValueAndLabel(DeliveryReturnStatus $status, int $expectedValue): void
    {
        $this->assertSame($expectedValue, $status->value);
        $this->assertNotEmpty($status->getLabel());
    }

    public function testFromException(): void
    {
        $this->expectException(\ValueError::class);
        DeliveryReturnStatus::from(999);
    }

    #[TestWith([0, DeliveryReturnStatus::Waiting])]
    #[TestWith([1, DeliveryReturnStatus::Appointment])]
    #[TestWith([2, DeliveryReturnStatus::Filled])]
    public function testTryFromValidValue(int $value, DeliveryReturnStatus $expected): void
    {
        $this->assertSame($expected, DeliveryReturnStatus::tryFrom($value));
    }

    #[TestWith([99])]
    #[TestWith([-1])]
    #[TestWith([100])]
    public function testTryFromInvalidValue(int $invalidValue): void
    {
        $this->assertNull(DeliveryReturnStatus::tryFrom($invalidValue));
    }

    public function testValueUniqueness(): void
    {
        $values = [];
        foreach (DeliveryReturnStatus::cases() as $case) {
            $this->assertNotContains($case->value, $values, 'Duplicate value found: ' . $case->value);
            $values[] = $case->value;
        }
    }

    public function testLabelUniqueness(): void
    {
        $labels = [];
        foreach (DeliveryReturnStatus::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotContains($label, $labels, 'Duplicate label found: ' . $label);
            $labels[] = $label;
        }
    }

    public function testToArray(): void
    {
        $enum = DeliveryReturnStatus::Waiting;
        $array = $enum->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertSame(0, $array['value']);
        $this->assertSame('用户未填写退货信', $array['label']);

        $enum2 = DeliveryReturnStatus::Filled;
        $array2 = $enum2->toArray();
        $this->assertIsArray($array2);
        $this->assertCount(2, $array2);
        $this->assertSame(2, $array2['value']);
        $this->assertSame('填写自行寄回运单号', $array2['label']);
    }
}
