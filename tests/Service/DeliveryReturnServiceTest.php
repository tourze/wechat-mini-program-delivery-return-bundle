<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

/**
 * @internal
 */
#[CoversClass(DeliveryReturnService::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryReturnServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 简单集成测试，无需特殊设置
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(DeliveryReturnService::class);
        $this->assertInstanceOf(DeliveryReturnService::class, $service);
    }

    public function testSyncReturnOrder(): void
    {
        $service = self::getService(DeliveryReturnService::class);
        $order = new DeliveryReturnOrder();
        $order->setReturnId('test_return_id');

        // 测试方法能正常调用，不抛出异常
        $this->expectNotToPerformAssertions();
        $service->syncReturnOrder($order);
    }
}
