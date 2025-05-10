<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;
use WechatMiniProgramDeliveryReturnBundle\Request\QueryStatusRequest;
use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

class DeliveryReturnServiceTest extends TestCase
{
    private MockObject&Client $client;
    private MockObject&LoggerInterface $logger;
    private MockObject&EntityManagerInterface $entityManager;
    private DeliveryReturnService $service;
    private Account $account;
    private DeliveryReturnOrder $order;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->service = new DeliveryReturnService(
            $this->client,
            $this->logger,
            $this->entityManager
        );
        
        $this->account = $this->createMock(Account::class);
        
        $this->order = new DeliveryReturnOrder();
        $this->order->setAccount($this->account);
        $this->order->setReturnId('return-id-123');
    }
    
    public function testSyncReturnOrderWithValidResponse(): void
    {
        $response = [
            'status' => DeliveryReturnStatus::Appointment->value,
            'waybill_id' => 'SF1234567890',
            'order_status' => DeliveryReturnOrderStatus::InTransit->value, 
            'delivery_name' => '顺丰速运',
            'delivery_id' => 'SF',
        ];
        
        $this->client
            ->expects($this->once())
            ->method('request')
            ->with($this->callback(function (QueryStatusRequest $request) {
                return $request->getReturnId() === 'return-id-123' && 
                    $request->getAccount() === $this->account;
            }))
            ->willReturn($response);
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->order);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');
        
        $this->service->syncReturnOrder($this->order);
        
        $this->assertSame(DeliveryReturnStatus::Appointment, $this->order->getStatus());
        $this->assertSame('SF1234567890', $this->order->getWaybillId());
        $this->assertSame(DeliveryReturnOrderStatus::InTransit, $this->order->getOrderStatus());
        $this->assertSame('顺丰速运', $this->order->getDeliveryName());
        $this->assertSame('SF', $this->order->getDeliveryId());
    }
    
    public function testSyncReturnOrderWithPartialResponse(): void
    {
        // 只返回状态，不返回其他字段
        $response = [
            'status' => DeliveryReturnStatus::Waiting->value,
        ];
        
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->order);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');
        
        $this->service->syncReturnOrder($this->order);
        
        $this->assertSame(DeliveryReturnStatus::Waiting, $this->order->getStatus());
        $this->assertNull($this->order->getWaybillId());
        $this->assertNull($this->order->getOrderStatus());
        $this->assertNull($this->order->getDeliveryName());
        $this->assertNull($this->order->getDeliveryId());
    }
    
    public function testSyncReturnOrderWithException(): void
    {
        $exception = new \Exception('API请求失败');
        
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);
        
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                '同步退货单信息失败',
                $this->callback(function (array $context) use ($exception) {
                    return $context['exception'] === $exception &&
                        $context['order'] === $this->order;
                })
            );
        
        $this->entityManager
            ->expects($this->never())
            ->method('persist');
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');
        
        $this->service->syncReturnOrder($this->order);
    }
} 