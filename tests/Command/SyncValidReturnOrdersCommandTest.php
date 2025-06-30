<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
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

class SyncValidReturnOrdersCommandTest extends TestCase
{
    private DeliveryReturnOrderRepository|MockObject $orderRepository;
    private EntityManagerInterface|MockObject $entityManager;
    private MessageBusInterface|MockObject $messageBus;
    private SyncValidReturnOrdersCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(DeliveryReturnOrderRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        
        $this->command = new SyncValidReturnOrdersCommand(
            $this->orderRepository,
            $this->entityManager,
            $this->messageBus
        );

        $application = new Application();
        $application->add($this->command);
        
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecuteWithMultipleOrders(): void
    {
        $order1 = $this->createMock(DeliveryReturnOrder::class);
        $order1->method('getId')->willReturn('1');
        $order1->method('getShopOrderId')->willReturn('ORDER-001');
        
        $order2 = $this->createMock(DeliveryReturnOrder::class);
        $order2->method('getId')->willReturn('2');
        $order2->method('getShopOrderId')->willReturn('ORDER-002');
        
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([$order1, $order2]);
            
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $this->orderRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);
            
        $this->messageBus->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                $this->assertInstanceOf(RunCommandMessage::class, $message);
                $this->assertSame(SyncSingleReturnOrderCommand::NAME, $message->getCommand());
                $this->assertArrayHasKey('shopOrderId', $message->getOptions());
                return true;
            }))
            ->willReturn(new Envelope(new \stdClass()));
            
        $this->entityManager->expects($this->exactly(2))
            ->method('detach');
        
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
            ->willReturn([]);
            
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $this->orderRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);
            
        $this->messageBus->expects($this->never())
            ->method('dispatch');
            
        $this->entityManager->expects($this->never())
            ->method('detach');
        
        $exitCode = $this->commandTester->execute([]);
        
        $this->assertSame(0, $exitCode);
    }
}