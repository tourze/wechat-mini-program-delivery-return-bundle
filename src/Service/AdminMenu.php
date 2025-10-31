<?php

declare(strict_types=1);

namespace WechatMiniProgramDeliveryReturnBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;

/**
 * 微信小程序退货管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('微信小程序')) {
            $item->addChild('微信小程序');
        }

        $wechatMenu = $item->getChild('微信小程序');
        if (null === $wechatMenu) {
            return;
        }

        // 添加退货管理子菜单
        if (null === $wechatMenu->getChild('退货管理')) {
            $wechatMenu->addChild('退货管理')
                ->setAttribute('icon', 'fas fa-undo')
            ;
        }

        $returnMenu = $wechatMenu->getChild('退货管理');
        if (null === $returnMenu) {
            return;
        }

        $returnMenu->addChild('退货单管理')
            ->setUri($this->linkGenerator->getCurdListPage(DeliveryReturnOrder::class))
            ->setAttribute('icon', 'fas fa-shipping-fast')
        ;
    }
}
