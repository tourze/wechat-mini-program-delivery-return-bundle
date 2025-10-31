<?php

namespace WechatMiniProgramDeliveryReturnBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 退货单状态
 *
 * @see https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/industry/express/business/express_sale_return.html
 */
enum DeliveryReturnStatus: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case Waiting = 0;
    case Appointment = 1;
    case Filled = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Waiting => '用户未填写退货信',
            self::Appointment => '预约上门取件',
            self::Filled => '填写自行寄回运单号',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::Waiting => self::INFO,
            self::Appointment => self::PRIMARY,
            self::Filled => self::SUCCESS,
        };
    }
}
