<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WechatMiniProgramDeliveryReturnBundle\Command\SyncSingleReturnOrderCommand;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Exception\ReturnOrderNotFoundException;
use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;
use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

/**
 * @internal
 *
 * @phpstan-ignore-next-line 命令测试使用单元测试而非集成测试，因为需要 Mock 依赖服务
 */
#[CoversClass(SyncSingleReturnOrderCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncSingleReturnOrderCommandTest extends TestCase
{
    /** @var MockObject&DeliveryReturnOrderRepository */
    private DeliveryReturnOrderRepository $orderRepository;

    /** @var MockObject&DeliveryReturnService */
    private DeliveryReturnService $deliveryReturnService;

    private SyncSingleReturnOrderCommand $command;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 使用具体类 DeliveryReturnOrderRepository 的原因：
        // 1. Repository 继承自 Doctrine 的抽象类，包含复杂的查询逻辑，需要测试具体行为
        // 2. 该类与 Entity 紧密耦合，抽象接口无法充分模拟其行为
        // 3. Command 测试需要验证具体的数据库操作方法调用
        $this->orderRepository = $this->createMock(DeliveryReturnOrderRepository::class);

        // 使用具体类 DeliveryReturnService 的原因：
        // 1. Service 包含复杂业务逻辑，需要测试具体的方法调用和参数传递
        // 2. 该类与外部API交互，抽象接口无法充分模拟其复杂行为
        // 3. Command 测试需要验证具体的服务方法调用序列
        $this->deliveryReturnService = $this->createMock(DeliveryReturnService::class);

        // @phpstan-ignore-next-line 使用 Mock 对象避免容器服务冲突
        $this->command = new SyncSingleReturnOrderCommand(
            $this->orderRepository,
            $this->deliveryReturnService
        );

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecuteWithValidOrder(): void
    {
        // 使用具体类 DeliveryReturnOrder 的原因：
        // 1. Entity 类包含复杂的属性映射和方法，抽象接口无法充分模拟其行为
        // 2. 测试需要验证具体的实体属性访问方法（getId、getShopOrderId）
        // 3. Command 测试需要模拟真实的实体对象行为
        $order = $this->createMock(DeliveryReturnOrder::class);
        $order->method('getId')->willReturn('1');
        $order->method('getShopOrderId')->willReturn('ORDER-001');

        $this->orderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['shopOrderId' => 'ORDER-001'])
            ->willReturn($order)
        ;

        $this->deliveryReturnService->expects($this->once())
            ->method('syncReturnOrder')
            ->with($order)
        ;

        $exitCode = $this->commandTester->execute(['shopOrderId' => 'ORDER-001']);

        $this->assertSame(0, $exitCode);
    }

    public function testExecuteWithOrderNotFound(): void
    {
        $this->orderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['shopOrderId' => 'ORDER-001'])
            ->willReturn(null)
        ;

        $this->deliveryReturnService->expects($this->never())
            ->method('syncReturnOrder')
        ;

        $this->expectException(ReturnOrderNotFoundException::class);
        $this->expectExceptionMessage('找不到退货单');

        $this->commandTester->execute(['shopOrderId' => 'ORDER-001']);
    }
}
