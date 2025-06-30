<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatMiniProgramDeliveryReturnBundle\WechatMiniProgramDeliveryReturnBundle;

class WechatMiniProgramDeliveryReturnBundleTest extends TestCase
{
    private WechatMiniProgramDeliveryReturnBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new WechatMiniProgramDeliveryReturnBundle();
    }

    public function testBundleExtendsSymfonyBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testBundleNamespace(): void
    {
        $reflectionClass = new \ReflectionClass($this->bundle);
        $this->assertEquals('WechatMiniProgramDeliveryReturnBundle', $reflectionClass->getNamespaceName());
    }

    public function testBundleClassName(): void
    {
        $this->assertEquals('WechatMiniProgramDeliveryReturnBundle\WechatMiniProgramDeliveryReturnBundle', get_class($this->bundle));
    }
}