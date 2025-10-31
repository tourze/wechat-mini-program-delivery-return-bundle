<?php

namespace WechatMiniProgramDeliveryReturnBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;
use WechatMiniProgramDeliveryReturnBundle\Request\QueryStatusRequest;

#[WithMonologChannel(channel: 'wechat_mini_program_delivery_return')]
#[Autoconfigure(public: true)]
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
            $returnId = $order->getReturnId();
            if (null === $returnId) {
                $this->logger->warning('退货ID为空，跳过同步状态', [
                    'orderId' => $order->getId(),
                ]);

                return;
            }

            $response = $this->fetchReturnStatus($returnId);
            if (null === $response) {
                return;
            }

            $this->updateOrderFromResponse($order, $response);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            $this->logger->error('同步退货单信息失败', [
                'exception' => $exception,
                'order' => $order,
            ]);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchReturnStatus(string $returnId): ?array
    {
        $request = new QueryStatusRequest();
        $request->setReturnId($returnId);
        $response = $this->client->request($request);

        if (!is_array($response)) {
            $this->logger->warning('响应格式不正确', [
                'response' => $response,
            ]);

            return null;
        }

        /** @var array<string, mixed> $response */
        return $response;
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateOrderFromResponse(DeliveryReturnOrder $order, array $response): void
    {
        if (isset($response['status']) && (is_int($response['status']) || is_string($response['status']))) {
            $order->setStatus(DeliveryReturnStatus::tryFrom($response['status']));
        }
        if (isset($response['waybill_id']) && is_string($response['waybill_id'])) {
            $order->setWaybillId($response['waybill_id']);
        }
        if (isset($response['order_status']) && (is_int($response['order_status']) || is_string($response['order_status']))) {
            $order->setOrderStatus(DeliveryReturnOrderStatus::tryFrom($response['order_status']));
        }
        if (isset($response['delivery_name']) && is_string($response['delivery_name'])) {
            $order->setDeliveryName($response['delivery_name']);
        }
        if (isset($response['delivery_id']) && is_string($response['delivery_id'])) {
            $order->setDeliveryId($response['delivery_id']);
        }
    }
}
