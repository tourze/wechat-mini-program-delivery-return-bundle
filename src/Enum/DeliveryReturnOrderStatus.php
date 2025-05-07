<?php

namespace WechatMiniProgramDeliveryReturnBundle\Enum;

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
enum DeliveryReturnOrderStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case Ordered = 0;
    case PickedUp = 1;
    case InTransit = 2;
    case OutForDelivery = 3;
    case Delivered = 4;
    case Exception = 5;
    case ProxyDelivered = 6;
    case PickUpFailed = 7;
    case DeliveryFailed = 8; // Failed delivery (rejected, out of delivery area)
    case Cancelled = 11;
    case Returning = 13;
    case Returned = 14;
    case Unknown = 99;

    public function getLabel(): string
    {
        return match ($this) {
            self::Ordered => '已下单待揽件',
            self::PickedUp => '已揽件',
            self::InTransit => '运输中',
            self::OutForDelivery => '派件中',
            self::Delivered => '已签收',
            self::Exception => '异常',
            self::ProxyDelivered => '代签收',
            self::PickUpFailed => '揽收失败',
            self::DeliveryFailed => '签收失败（拒收，超区）',
            self::Cancelled => '已取消',
            self::Returning => '退件中',
            self::Returned => '已退件',
            self::Unknown => '未知',
        };
    }
}
