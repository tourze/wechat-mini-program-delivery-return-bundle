<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatMiniProgramDeliveryReturnBundle\DependencyInjection\WechatMiniProgramDeliveryReturnExtension;

/**
 * @internal
 */
#[CoversClass(WechatMiniProgramDeliveryReturnExtension::class)]
final class WechatMiniProgramDeliveryReturnExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private WechatMiniProgramDeliveryReturnExtension $extension;

    public function testGetAlias(): void
    {
        $alias = $this->extension->getAlias();
        $this->assertSame('wechat_mini_program_delivery_return', $alias);
    }

    public function testServicesYamlFileExists(): void
    {
        $servicesFile = dirname(__DIR__, 2) . '/src/Resources/config/services.yaml';
        $this->assertFileExists($servicesFile);
    }

    public function testFileLocatorPath(): void
    {
        $expectedPath = dirname(__DIR__, 2) . '/src/Resources/config';
        $this->assertDirectoryExists($expectedPath);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new WechatMiniProgramDeliveryReturnExtension();
    }
}
