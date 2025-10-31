<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use HttpClientBundle\Tests\Request\RequestTestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramDeliveryReturnBundle\Request\UnbindRequest;

/**
 * @internal
 */
#[CoversClass(UnbindRequest::class)]
final class UnbindRequestTest extends RequestTestCase
{
    private UnbindRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new UnbindRequest();

        // 使用具体类 Account 的原因：
        // 1. Account Entity 包含复杂的属性映射和方法，抽象接口无法充分模拟其行为
        // 2. Request 测试需要验证与账户相关的具体业务逻辑
        // 3. 该类与微信小程序配置紧密相关，需要模拟真实的账户对象行为
        $this->account = $this->createMock(Account::class);

        $this->request->setAccount($this->account);
        $this->request->setReturnId('return-id-123');
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/express/delivery/return/unbind', $this->request->getRequestPath());
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

        // 测试更改属性
        // 使用具体类 Account 的原因：
        // 1. Account Entity 包含复杂的属性映射和方法，抽象接口无法充分模拟其行为
        // 2. Request 测试需要验证与账户相关的具体业务逻辑
        // 3. 该类与微信小程序配置紧密相关，需要模拟真实的账户对象行为
        $newAccount = $this->createMock(Account::class);
        $this->request->setAccount($newAccount);
        $this->assertSame($newAccount, $this->request->getAccount());

        $this->request->setReturnId('new-return-id');
        $this->assertSame('new-return-id', $this->request->getReturnId());
    }

    public function testWithEmptyReturnId(): void
    {
        $request = new UnbindRequest();
        $request->setAccount($this->account);

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('must not be accessed before initialization');
        $returnId = $request->getReturnId();
        // The above call should throw an exception, so this line should never be reached
        self::fail('Expected exception was not thrown');
    }
}
