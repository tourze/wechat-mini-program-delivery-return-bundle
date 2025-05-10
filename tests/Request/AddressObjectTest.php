<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramDeliveryReturnBundle\Request\AddressObject;

class AddressObjectTest extends TestCase
{
    public function testSetAndGetProperties(): void
    {
        $address = new AddressObject();
        
        $address->setName('测试姓名');
        $this->assertSame('测试姓名', $address->getName());
        
        $address->setMobile('13800138000');
        $this->assertSame('13800138000', $address->getMobile());
        
        // 默认值测试
        $this->assertSame('中国', $address->getCountry());
        
        $address->setCountry('美国');
        $this->assertSame('美国', $address->getCountry());
        
        $address->setProvince('北京市');
        $this->assertSame('北京市', $address->getProvince());
        
        $address->setCity('北京市');
        $this->assertSame('北京市', $address->getCity());
        
        $address->setArea('海淀区');
        $this->assertSame('海淀区', $address->getArea());
        
        $address->setAddress('详细地址');
        $this->assertSame('详细地址', $address->getAddress());
    }
    
    public function testFromArray(): void
    {
        $data = [
            'name' => '测试姓名',
            'mobile' => '13800138000',
            'country' => '中国',
            'province' => '北京市',
            'city' => '北京市',
            'area' => '海淀区',
            'address' => '详细地址',
        ];
        
        $address = AddressObject::fromArray($data);
        
        $this->assertInstanceOf(AddressObject::class, $address);
        $this->assertSame('测试姓名', $address->getName());
        $this->assertSame('13800138000', $address->getMobile());
        $this->assertSame('中国', $address->getCountry());
        $this->assertSame('北京市', $address->getProvince());
        $this->assertSame('北京市', $address->getCity());
        $this->assertSame('海淀区', $address->getArea());
        $this->assertSame('详细地址', $address->getAddress());
    }
    
    public function testToArray(): void
    {
        $address = new AddressObject();
        $address->setName('测试姓名');
        $address->setMobile('13800138000');
        $address->setCountry('中国');
        $address->setProvince('北京市');
        $address->setCity('北京市');
        $address->setArea('海淀区');
        $address->setAddress('详细地址');
        
        $expected = [
            'name' => '测试姓名',
            'mobile' => '13800138000',
            'country' => '中国',
            'province' => '北京市',
            'city' => '北京市',
            'area' => '海淀区',
            'address' => '详细地址',
        ];
        
        $this->assertSame($expected, $address->toArray());
    }
    
    public function testRoundTrip(): void
    {
        $data = [
            'name' => '测试姓名',
            'mobile' => '13800138000',
            'country' => '中国',
            'province' => '北京市',
            'city' => '北京市',
            'area' => '海淀区',
            'address' => '详细地址',
        ];
        
        $address = AddressObject::fromArray($data);
        $result = $address->toArray();
        
        $this->assertSame($data, $result);
    }
} 