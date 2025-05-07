<?php

namespace WechatMiniProgramDeliveryReturnBundle\Request;

use WechatMiniProgramBundle\Request\WithAccountRequest;

/**
 *  查询退货 ID 状态
 *  本接口用于商家查询用户退货状态（是否填写退货信息）及追踪用户退货物流，方便仓库收货。通过本接口查询退货 ID 状态，其中status是退货ID状态，order_status是退货 ID 对应的用户运单号的状态。
 *
 * @see https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/industry/express/business/express_sale_return.html
 */
class QueryStatusRequest extends WithAccountRequest
{
    private string $returnId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/express/delivery/return/get';
    }

    public function getRequestOptions(): ?array
    {
        $payload = [
            'return_id' => $this->getReturnId(),
        ];

        return [
            'json' => $payload,
        ];
    }

    public function getReturnId(): string
    {
        return $this->returnId;
    }

    public function setReturnId(string $returnId): void
    {
        $this->returnId = $returnId;
    }
}
