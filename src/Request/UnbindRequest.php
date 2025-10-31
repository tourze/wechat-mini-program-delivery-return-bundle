<?php

namespace WechatMiniProgramDeliveryReturnBundle\Request;

use WechatMiniProgramBundle\Request\WithAccountRequest;

/**
 * 解绑退货 ID
 * 当商家同意退货申请之后，与用户达成协商「无需退货」时，可以通过本接口可以解除商家退货单与退货 ID的绑定。考虑到预约快递员上门取件的情况在用户侧发生，因此只有当用户是自主填写运单号情况下才支持解绑退货 ID 。
 *
 * @see https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/industry/express/business/express_sale_return.html
 */
class UnbindRequest extends WithAccountRequest
{
    private string $returnId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/express/delivery/return/unbind';
    }

    /**
     * @return array<string, mixed>|null
     */
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
