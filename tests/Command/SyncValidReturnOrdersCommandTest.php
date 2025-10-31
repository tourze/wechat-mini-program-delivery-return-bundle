<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;
use WechatMiniProgramDeliveryReturnBundle\Command\SyncSingleReturnOrderCommand;
use WechatMiniProgramDeliveryReturnBundle\Command\SyncValidReturnOrdersCommand;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;

/**
 * @internal
 *
 * @phpstan-ignore-next-line 命令测试使用单元测试而非集成测试，因为需要 Mock 依赖服务
 */
#[CoversClass(SyncValidReturnOrdersCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncValidReturnOrdersCommandTest extends TestCase
{
    /** @var MockObject&DeliveryReturnOrderRepository */
    private DeliveryReturnOrderRepository $orderRepository;

    /** @var MockObject&MessageBusInterface */
    private MessageBusInterface $messageBus;

    private SyncValidReturnOrdersCommand $command;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 使用具体类 DeliveryReturnOrderRepository 的原因：
        // 1. Repository 继承自 Doctrine 的抽象类，包含复杂的查询逻辑，需要测试具体行为
        // 2. 该类与 Entity 紧密耦合，抽象接口无法充分模拟其行为
        // 3. Command 测试需要验证具体的数据库操作方法调用
        $this->orderRepository = $this->createMock(DeliveryReturnOrderRepository::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);

        // 这里由于要在测试中使用 Mock，暂时忽略 PHPStan 规则 - 直接创建实例
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // @phpstan-ignore-next-line 使用 Mock 对象避免容器服务冲突
        $this->command = new SyncValidReturnOrdersCommand(
            $this->orderRepository,
            $entityManager,
            $this->messageBus
        );

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecuteWithMultipleOrders(): void
    {
        // 使用具体类 DeliveryReturnOrder 的原因：
        // 1. Entity 类包含复杂的属性映射和方法，抽象接口无法充分模拟其行为
        // 2. 测试需要验证具体的实体属性访问方法（getId、getShopOrderId）
        // 3. Command 测试需要模拟真实的实体对象行为
        $order1 = $this->createMock(DeliveryReturnOrder::class);
        $order1->method('getId')->willReturn('1');
        $order1->method('getShopOrderId')->willReturn('ORDER-001');

        // 使用具体类 DeliveryReturnOrder 的原因：
        // 1. Entity 类包含复杂的属性映射和方法，抽象接口无法充分模拟其行为
        // 2. 测试需要验证具体的实体属性访问方法（getId、getShopOrderId）
        // 3. Command 测试需要模拟真实的实体对象行为
        $order2 = $this->createMock(DeliveryReturnOrder::class);
        $order2->method('getId')->willReturn('2');
        $order2->method('getShopOrderId')->willReturn('ORDER-002');

        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([$order1, $order2])
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $this->orderRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder)
        ;

        $this->messageBus->expects($this->exactly(2))
            ->method('dispatch')
            ->with(self::callback(function ($message) {
                self::assertInstanceOf(RunCommandMessage::class, $message);
                self::assertSame(SyncSingleReturnOrderCommand::NAME, $message->getCommand());
                self::assertArrayHasKey('shopOrderId', $message->getOptions());

                return true;
            }))
            ->willReturn(new Envelope(new \stdClass()))
        ;

        // EntityManager detach calls are handled internally

        $exitCode = $this->commandTester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('开始异步检查：1', $this->commandTester->getDisplay());
        $this->assertStringContainsString('开始异步检查：2', $this->commandTester->getDisplay());
    }

    public function testExecuteWithNoOrders(): void
    {
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([])
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $this->orderRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder)
        ;

        $this->messageBus->expects($this->never())
            ->method('dispatch')
        ;

        // EntityManager detach calls are handled internally

        $exitCode = $this->commandTester->execute([]);

        $this->assertSame(0, $exitCode);
    }
}
