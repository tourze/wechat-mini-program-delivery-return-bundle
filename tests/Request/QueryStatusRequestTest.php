<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramDeliveryReturnBundle\Request\QueryStatusRequest;

/**
 * @internal
 */
#[CoversClass(QueryStatusRequest::class)]
final class QueryStatusRequestTest extends RequestTestCase
{
    private QueryStatusRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new QueryStatusRequest();
        // 使用具体类 Account 的原因：
        // 1. Account Entity 包含复杂的属性映射和业务逻辑，抽象接口无法充分模拟
        // 2. Request 测试需要验证与账户相关的真实关联关系
        // 3. 该类与微信小程序配置紧密相关，需要模拟真实的账户对象行为
        $this->account = $this->createMock(Account::class);

        $this->request->setAccount($this->account);
        $this->request->setReturnId('return-id-123');
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/express/delivery/return/get', $this->request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('return_id', $json);
        $this->assertSame('return-id-123', $json['return_id']);
    }

    public function testSetAndGetProperties(): void
    {
        $this->assertSame($this->account, $this->request->getAccount());
        $this->assertSame('return-id-123', $this->request->getReturnId());

        // 使用具体类 Account 的原因：
        // 1. Account Entity 包含复杂的属性映射和业务逻辑，抽象接口无法充分模拟
        // 2. Request 测试需要验证账户属性更改后的真实行为
        // 3. 该类与微信小程序配置紧密相关，需要模拟真实的账户对象行为
        $newAccount = $this->createMock(Account::class);
        $this->request->setAccount($newAccount);
        $this->assertSame($newAccount, $this->request->getAccount());

        $this->request->setReturnId('new-return-id');
        $this->assertSame('new-return-id', $this->request->getReturnId());
    }

    public function testWithEmptyReturnId(): void
    {
        $request = new QueryStatusRequest();
        $request->setAccount($this->account);

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('must not be accessed before initialization');
        $returnId = $request->getReturnId();
        // The above call should throw an exception, so this line should never be reached
        self::fail('Expected exception was not thrown');
    }
}
