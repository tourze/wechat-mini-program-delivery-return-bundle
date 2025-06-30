<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WechatMiniProgramDeliveryReturnBundle\Command\SyncSingleReturnOrderCommand;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Exception\ReturnOrderNotFoundException;
use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;
use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

class SyncSingleReturnOrderCommandTest extends TestCase
{
    private DeliveryReturnOrderRepository|MockObject $orderRepository;
    private DeliveryReturnService|MockObject $deliveryReturnService;
    private SyncSingleReturnOrderCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(DeliveryReturnOrderRepository::class);
        $this->deliveryReturnService = $this->createMock(DeliveryReturnService::class);
        
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
        $shopOrderId = 'TEST-ORDER-001';
        $order = new DeliveryReturnOrder();
        
        $this->orderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['shopOrderId' => $shopOrderId])
            ->willReturn($order);
            
        $this->deliveryReturnService->expects($this->once())
            ->method('syncReturnOrder')
            ->with($order);
        
        $exitCode = $this->commandTester->execute([
            'shopOrderId' => $shopOrderId,
        ]);
        
        $this->assertSame(0, $exitCode);
    }

    public function testExecuteWithOrderNotFound(): void
    {
        $shopOrderId = 'INVALID-ORDER';
        
        $this->orderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['shopOrderId' => $shopOrderId])
            ->willReturn(null);
            
        $this->deliveryReturnService->expects($this->never())
            ->method('syncReturnOrder');
        
        $this->expectException(ReturnOrderNotFoundException::class);
        $this->expectExceptionMessage('找不到退货单');
        
        $this->commandTester->execute([
            'shopOrderId' => $shopOrderId,
        ]);
    }
}