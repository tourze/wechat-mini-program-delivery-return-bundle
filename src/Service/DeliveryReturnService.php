<?php

namespace WechatMiniProgramDeliveryReturnBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;
use WechatMiniProgramDeliveryReturnBundle\Request\QueryStatusRequest;

class DeliveryReturnService
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 同步退货单信息
     */
    public function syncReturnOrder(DeliveryReturnOrder $order): void
    {
        try {
            $request = new QueryStatusRequest();
            $request->setAccount($order->getAccount());
            $request->setReturnId($order->getReturnId());
            $response = $this->client->request($request);
            $order->setStatus(DeliveryReturnStatus::tryFrom($response['status']));
            if ((bool) isset($response['waybill_id'])) {
                $order->setWaybillId($response['waybill_id']);
            }
            if ((bool) isset($response['order_status'])) {
                $order->setOrderStatus(DeliveryReturnOrderStatus::tryFrom($response['order_status']));
            }
            if ((bool) isset($response['delivery_name'])) {
                $order->setDeliveryName($response['delivery_name']);
            }
            if ((bool) isset($response['delivery_id'])) {
                $order->setDeliveryId($response['delivery_id']);
            }
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            $this->logger->error('同步退货单信息失败', [
                'exception' => $exception,
                'order' => $order,
            ]);
        }
    }
}
