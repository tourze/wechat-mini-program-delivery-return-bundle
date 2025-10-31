<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\WechatMiniProgramUserContracts\UserInterface;
use Tourze\WechatMiniProgramUserContracts\UserLoaderInterface;
use WechatMiniProgramAuthBundle\Entity\User;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\EventSubscriber\ReturnOrderListener;
use WechatMiniProgramDeliveryReturnBundle\Exception\DeliveryReturnOrderException;
use WechatMiniProgramDeliveryReturnBundle\Request\AddRequest;
use WechatMiniProgramDeliveryReturnBundle\Request\UnbindRequest;

/**
 * @internal
 */
#[CoversClass(ReturnOrderListener::class)]
#[RunTestsInSeparateProcesses]
final class ReturnOrderListenerTest extends AbstractIntegrationTestCase
{
    private MockObject&Client $client;

    private MockObject&UserLoaderInterface $userLoader;

    private MockObject&LoggerInterface $logger;

    private ReturnOrderListener $listener;

    protected function onSetUp(): void
    {
        // Initialize services once in setup to avoid "service is already initialized" error
        $this->createReturnOrderListener();
    }

    private function createReturnOrderListener(): void
    {
        // Mock具体类说明: WechatMiniProgramBundle\Service\Client是微信小程序的HTTP客户端类，
        // 没有对应的接口定义，测试中需要模拟其request方法来验证网络请求逻辑。
        // 使用具体类Mock是合理的，因为需要模拟具体的HTTP客户端行为。
        // 替代方案：可以创建一个测试专用的HTTP客户端接口，但当前Mock方式更直接可用。
        $this->client = $this->createMock(Client::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // 由于 ReturnOrderListener 是 Doctrine EntityListener，直接从容器获取会导致
        // "service is already initialized" 错误，因此我们直接实例化它
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
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

        // 使用具体类 Account 实例的原因：
        // 1. Account Entity 包含真实的属性设置和获取方法，需要测试具体的属性行为
        // 2. 测试需要验证 Account 与其他组件的真实交互逻辑
        // 3. 该类包含微信小程序配置信息，需要模拟真实的配置对象
        $account = new Account();
        $account->setAppId('test-app-id');

        // 使用具体类 User 的原因：
        // 1. User Entity 包含复杂的用户属性和方法，抽象接口无法充分模拟其行为
        // 2. EventSubscriber 测试需要验证具体的用户相关业务逻辑
        // 3. 该类与微信小程序用户系统紧密相关，需要模拟真实的用户对象行为
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
            'address' => '科技园路1号',
        ]);
        $order->setOrderPath('/pages/order');
        $order->setGoodsList([['name' => 'item1', 'url' => 'https://example.com/item1.jpg']]);
        $order->setOrderPrice(10000);

        $this->userLoader->expects($this->once())
            ->method('loadUserByOpenId')
            ->with($openId)
            ->willReturn($user)
        ;

        $this->client->expects($this->once())
            ->method('request')
            ->with(self::isInstanceOf(AddRequest::class))
            ->willReturn(['return_id' => $returnId])
        ;

        $this->listener->prePersist($order);

        $this->assertEquals($returnId, $order->getReturnId());
    }

    public function testPrePersistThrowsExceptionWhenUserNotFound(): void
    {
        $this->expectException(DeliveryReturnOrderException::class);
        $this->expectExceptionMessage('找不到小程序用户');

        $order = new DeliveryReturnOrder();
        $order->setOpenId('non-existent-openid');
        $order->setShopOrderId('shop-order-123');
        $order->setOrderPath('/pages/order');
        $order->setGoodsList([['name' => 'item1', 'url' => 'https://example.com/item1.jpg']]);

        $this->userLoader->expects($this->once())
            ->method('loadUserByOpenId')
            ->willReturn(null)
        ;

        $this->listener->prePersist($order);
    }

    public function testPrePersistThrowsExceptionWhenUserTypeWrong(): void
    {
        $this->expectException(DeliveryReturnOrderException::class);
        $this->expectExceptionMessage('用户类型不正确');

        // Create a mock object that implements UserInterface but is not of type User
        $wrongTypeUser = $this->createMock(UserInterface::class);

        $order = new DeliveryReturnOrder();
        $order->setOpenId('test-openid');
        $order->setShopOrderId('shop-order-123');
        $order->setOrderPath('/pages/order');
        $order->setGoodsList([['name' => 'item1', 'url' => 'https://example.com/item1.jpg']]);

        $this->userLoader->expects($this->once())
            ->method('loadUserByOpenId')
            ->willReturn($wrongTypeUser)
        ;

        $this->listener->prePersist($order);
    }

    public function testPostRemoveSuccess(): void
    {
        $returnId = 'test-return-id';

        $order = new DeliveryReturnOrder();
        $order->setReturnId($returnId);

        $this->client->expects($this->once())
            ->method('request')
            ->with(self::callback(function (UnbindRequest $request) use ($returnId) {
                return $request->getReturnId() === $returnId;
            }))
        ;

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
            ->willThrowException($exception)
        ;

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                '退货组件删除ReturnOrder并同步删除远程时失败',
                self::callback(function (array $context) use ($order, $exception) {
                    return $context['returnOrder'] === $order
                        && $context['exception'] === $exception;
                })
            )
        ;

        $this->listener->postRemove($order);
    }
}
