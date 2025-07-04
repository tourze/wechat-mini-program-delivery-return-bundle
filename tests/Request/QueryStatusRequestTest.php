<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramDeliveryReturnBundle\Request\QueryStatusRequest;

class QueryStatusRequestTest extends TestCase
{
    private QueryStatusRequest $request;
    private Account $account;
    
    protected function setUp(): void
    {
        $this->request = new QueryStatusRequest();
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
        $this->assertArrayHasKey('json', $options);
        
        $json = $options['json'];
        $this->assertArrayHasKey('return_id', $json);
        $this->assertSame('return-id-123', $json['return_id']);
    }
    
    public function testSetAndGetProperties(): void
    {
        $this->assertSame($this->account, $this->request->getAccount());
        $this->assertSame('return-id-123', $this->request->getReturnId());
        
        // 测试更改属性
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
        $this->fail('Expected exception was not thrown');
    }
} 