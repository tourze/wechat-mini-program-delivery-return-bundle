<?php

namespace WechatMiniProgramDeliveryReturnBundle\Request;

use WechatMiniProgramBundle\Request\WithAccountRequest;

/**
 *  创建退货 ID
 *
 * @see https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/industry/express/business/express_sale_return.html
 */
class AddRequest extends WithAccountRequest
{
    /**
     * 商家内部系统使用的退货编号
     */
    private string $shopOrderId;

    private AddressObject $bizAddr;

    private ?AddressObject $userAddr = null;

    /**
     * @var string 退货用户的openid
     */
    private string $openid;

    /**
     * @var string 退货订单在商家小程序的path
     */
    private string $orderPath;

    /**
     * @var array<int, array<string, mixed>> 退货商品list
     *            {
     *            "name":"xxx",//退货商品的名称
     *            "url":"xxx"//退货商品图片的url
     *            }
     */
    private array $goodsList;

    /**
     * @var int 退货订单的价格
     */
    private int $orderPrice;

    public function getRequestPath(): string
    {
        return '/cgi-bin/express/delivery/return/add';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $payload = [
            'shop_order_id' => $this->getShopOrderId(),
            'biz_addr' => $this->getBizAddr()->toArray(),
            'openid' => $this->getOpenid(),
            'order_path' => $this->getOrderPath(),
            'goods_list' => $this->getGoodsList(),
            'order_price' => $this->getOrderPrice(),
        ];

        if (null !== $this->getUserAddr()) {
            $payload['user_addr'] = $this->getUserAddr()->toArray();
        }

        return [
            'json' => $payload,
        ];
    }

    public function getShopOrderId(): string
    {
        return $this->shopOrderId;
    }

    public function setShopOrderId(string $shopOrderId): void
    {
        $this->shopOrderId = $shopOrderId;
    }

    public function getBizAddr(): AddressObject
    {
        return $this->bizAddr;
    }

    public function setBizAddr(AddressObject $bizAddr): void
    {
        $this->bizAddr = $bizAddr;
    }

    public function getUserAddr(): ?AddressObject
    {
        return $this->userAddr;
    }

    public function setUserAddr(?AddressObject $userAddr): void
    {
        $this->userAddr = $userAddr;
    }

    public function getOpenid(): string
    {
        return $this->openid;
    }

    public function setOpenid(string $openid): void
    {
        $this->openid = $openid;
    }

    public function getOrderPath(): string
    {
        return $this->orderPath;
    }

    public function setOrderPath(string $orderPath): void
    {
        $this->orderPath = $orderPath;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getGoodsList(): array
    {
        return $this->goodsList;
    }

    /**
     * @param array<int, array<string, mixed>> $goodsList
     */
    public function setGoodsList(array $goodsList): void
    {
        $this->goodsList = $goodsList;
    }

    public function getOrderPrice(): int
    {
        return $this->orderPrice;
    }

    public function setOrderPrice(int $orderPrice): void
    {
        $this->orderPrice = $orderPrice;
    }
}
