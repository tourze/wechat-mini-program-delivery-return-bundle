<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramDeliveryReturnBundle\Request\AddRequest;
use WechatMiniProgramDeliveryReturnBundle\Request\AddressObject;

/**
 * @internal
 */
#[CoversClass(AddRequest::class)]
final class AddRequestTest extends RequestTestCase
{
    private AddRequest $request;

    private AddressObject $bizAddr;

    private AddressObject $userAddr;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new AddRequest();

        $this->bizAddr = new AddressObject();
        $this->bizAddr->setName('商家');
        $this->bizAddr->setMobile('13800138000');
        $this->bizAddr->setProvince('北京市');
        $this->bizAddr->setCity('北京市');
        $this->bizAddr->setArea('海淀区');
        $this->bizAddr->setAddress('详细地址');

        $this->userAddr = new AddressObject();
        $this->userAddr->setName('用户');
        $this->userAddr->setMobile('13900139000');
        $this->userAddr->setProvince('上海市');
        $this->userAddr->setCity('上海市');
        $this->userAddr->setArea('浦东新区');
        $this->userAddr->setAddress('详细地址');

        // 使用具体类 Account 的原因：
        // 1. Account Entity 包含复杂的属性映射和业务逻辑，抽象接口无法充分模拟
        // 2. Request 测试需要验证与账户相关的真实关联关系
        // 3. 该类与微信小程序配置紧密相关，需要模拟真实的账户对象行为
        $this->account = $this->createMock(Account::class);

        $this->request->setAccount($this->account);
        $this->request->setShopOrderId('TEST-ORDER-123');
        $this->request->setBizAddr($this->bizAddr);
        $this->request->setOpenid('test-openid');
        $this->request->setOrderPath('pages/order/detail');
        $this->request->setGoodsList([
            [
                'name' => '测试商品',
                'url' => 'https://example.com/image.jpg',
            ],
        ]);
        $this->request->setOrderPrice(10000);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/express/delivery/return/add', $this->request->getRequestPath());
    }

    public function testGetRequestOptionsWithoutUserAddr(): void
    {
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('shop_order_id', $json);
        $this->assertArrayHasKey('biz_addr', $json);
        $this->assertArrayHasKey('openid', $json);
        $this->assertArrayHasKey('order_path', $json);
        $this->assertArrayHasKey('goods_list', $json);
        $this->assertArrayHasKey('order_price', $json);
        $this->assertArrayNotHasKey('user_addr', $json);

        $this->assertSame('TEST-ORDER-123', $json['shop_order_id']);
        $this->assertSame($this->bizAddr->toArray(), $json['biz_addr']);
        $this->assertSame('test-openid', $json['openid']);
        $this->assertSame('pages/order/detail', $json['order_path']);
        $this->assertSame([
            [
                'name' => '测试商品',
                'url' => 'https://example.com/image.jpg',
            ],
        ], $json['goods_list']);
        $this->assertSame(10000, $json['order_price']);
    }

    public function testGetRequestOptionsWithUserAddr(): void
    {
        $this->request->setUserAddr($this->userAddr);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('user_addr', $json);
        $this->assertSame($this->userAddr->toArray(), $json['user_addr']);
    }

    public function testSetAndGetProperties(): void
    {
        $this->assertSame($this->account, $this->request->getAccount());
        $this->assertSame('TEST-ORDER-123', $this->request->getShopOrderId());
        $this->assertSame($this->bizAddr, $this->request->getBizAddr());
        $this->assertNull($this->request->getUserAddr());
        $this->assertSame('test-openid', $this->request->getOpenid());
        $this->assertSame('pages/order/detail', $this->request->getOrderPath());
        $this->assertSame([
            [
                'name' => '测试商品',
                'url' => 'https://example.com/image.jpg',
            ],
        ], $this->request->getGoodsList());
        $this->assertSame(10000, $this->request->getOrderPrice());

        // 使用具体类 Account 的原因：
        // 1. Account Entity 包含复杂的属性映射和业务逻辑，抽象接口无法充分模拟
        // 2. Request 测试需要验证账户属性更改后的真实行为
        // 3. 该类与微信小程序配置紧密相关，需要模拟真实的账户对象行为
        $newAccount = $this->createMock(Account::class);
        $this->request->setAccount($newAccount);
        $this->assertSame($newAccount, $this->request->getAccount());

        $this->request->setUserAddr($this->userAddr);
        $this->assertSame($this->userAddr, $this->request->getUserAddr());
    }
}
