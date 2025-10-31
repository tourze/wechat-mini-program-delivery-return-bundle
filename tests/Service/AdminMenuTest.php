<?php

declare(strict_types=1);

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatMiniProgramDeliveryReturnBundle\Service\AdminMenu;

/**
 * AdminMenu服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu($rootItem);

        // 验证菜单结构
        $wechatMenu = $rootItem->getChild('微信小程序');
        self::assertNotNull($wechatMenu);

        $returnMenu = $wechatMenu->getChild('退货管理');
        self::assertNotNull($returnMenu);

        self::assertNotNull($returnMenu->getChild('退货单管理'));
    }
}
