<?php

namespace WechatMiniProgramDeliveryReturnBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use WechatMiniProgramAuthBundle\WechatMiniProgramAuthBundle;
use WechatMiniProgramBundle\WechatMiniProgramBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class WechatMiniProgramDeliveryReturnBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            WechatMiniProgramBundle::class => ['all' => true],
            WechatMiniProgramAuthBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
