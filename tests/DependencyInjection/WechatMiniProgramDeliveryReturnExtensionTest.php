<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatMiniProgramDeliveryReturnBundle\DependencyInjection\WechatMiniProgramDeliveryReturnExtension;

class WechatMiniProgramDeliveryReturnExtensionTest extends TestCase
{
    private WechatMiniProgramDeliveryReturnExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatMiniProgramDeliveryReturnExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $configs = [];
        
        $this->extension->load($configs, $this->container);
        
        // 验证服务是否已注册
        $this->assertTrue($this->container->has('WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService'));
        $this->assertTrue($this->container->has('WechatMiniProgramDeliveryReturnBundle\Command\SyncSingleReturnOrderCommand'));
        $this->assertTrue($this->container->has('WechatMiniProgramDeliveryReturnBundle\Command\SyncValidReturnOrdersCommand'));
    }
}