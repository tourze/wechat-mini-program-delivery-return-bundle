<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\WechatMiniProgramUserContracts\UserLoaderInterface;
use WechatMiniProgramAuthBundle\Entity\User;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\EventSubscriber\ReturnOrderListener;
use WechatMiniProgramDeliveryReturnBundle\Request\AddRequest;
use WechatMiniProgramDeliveryReturnBundle\Request\UnbindRequest;

class ReturnOrderListenerTest extends TestCase
{
    private MockObject&Client $client;
    private MockObject&UserLoaderInterface $userLoader;
    private MockObject&LoggerInterface $logger;
    private ReturnOrderListener $listener;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->listener = new ReturnOrderListener(
            $this->client,
            $this->userLoader,
            $this->logger
        );
    }

    public function testPrePersistSuccess(): void
    {
        $openId = 'test-openid';
        $returnId = 'test-return-id';
        
        $account = new Account();
        $account->setAppId('test-app-id');
        
        $user = $this->createMock(User::class);
        $user->method('getAccount')->willReturn($account);
        
        $order = new DeliveryReturnOrder();
        $order->setOpenId($openId);
        $order->setShopOrderId('shop-order-123');
        $order->setBizAddress([
            'name' => 'Test',
            'mobile' => '123456',
            'country' => '中国',
            'province' => '广东省',
            'city' => '深圳市',
            'area' => '南山区',
            'address' => '科技园路1号'
        ]);
        $order->setOrderPath('/pages/order');
        $order->setGoodsList(['item1']);
        $order->setOrderPrice(10000);
        
        $this->userLoader->expects($this->once())
            ->method('loadUserByOpenId')
            ->with($openId)
            ->willReturn($user);
        
        $this->client->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(AddRequest::class))
            ->willReturn(['return_id' => $returnId]);
        
        $this->listener->prePersist($order);
        
        $this->assertEquals($returnId, $order->getReturnId());
    }

    public function testPrePersistThrowsExceptionWhenUserNotFound(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到小程序用户');
        
        $order = new DeliveryReturnOrder();
        $order->setOpenId('non-existent-openid');
        
        $this->userLoader->expects($this->once())
            ->method('loadUserByOpenId')
            ->willReturn(null);
        
        $this->listener->prePersist($order);
    }

    public function testPrePersistThrowsExceptionWhenUserTypeWrong(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('用户类型不正确');
        
        // Create a mock object that implements UserInterface but is not of type User
        $wrongTypeUser = $this->createMock(\Tourze\WechatMiniProgramUserContracts\UserInterface::class);
        
        $order = new DeliveryReturnOrder();
        $order->setOpenId('test-openid');
        
        $this->userLoader->expects($this->once())
            ->method('loadUserByOpenId')
            ->willReturn($wrongTypeUser);
        
        $this->listener->prePersist($order);
    }

    public function testPostRemoveSuccess(): void
    {
        $returnId = 'test-return-id';
        
        $order = new DeliveryReturnOrder();
        $order->setReturnId($returnId);
        
        $this->client->expects($this->once())
            ->method('request')
            ->with($this->callback(function (UnbindRequest $request) use ($returnId) {
                return $request->getReturnId() === $returnId;
            }));
        
        $this->logger->expects($this->never())->method('error');
        
        $this->listener->postRemove($order);
    }

    public function testPostRemoveLogsErrorOnException(): void
    {
        $returnId = 'test-return-id';
        $exception = new \RuntimeException('API error');
        
        $order = new DeliveryReturnOrder();
        $order->setReturnId($returnId);
        
        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);
        
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                '退货组件删除ReturnOrder并同步删除远程时失败',
                $this->callback(function (array $context) use ($order, $exception) {
                    return $context['returnOrder'] === $order 
                        && $context['exception'] === $exception;
                })
            );
        
        $this->listener->postRemove($order);
    }
}