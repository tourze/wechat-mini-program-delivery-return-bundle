<?php

namespace WechatMiniProgramDeliveryReturnBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;
use Tourze\WechatMiniProgramUserContracts\UserLoaderInterface;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Request\AddRequest;
use WechatMiniProgramDeliveryReturnBundle\Request\AddressObject;
use WechatMiniProgramDeliveryReturnBundle\Request\UnbindRequest;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: DeliveryReturnOrder::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: DeliveryReturnOrder::class)]
class ReturnOrderListener
{
    public function __construct(
        private readonly Client $client,
        private readonly UserLoaderInterface $userLoader,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function prePersist(DeliveryReturnOrder $obj): void
    {
        $user = $this->userLoader->loadUserByOpenId($obj->getOpenId());
        if (!$user) {
            throw new ApiException('找不到小程序用户');
        }

        $request = new AddRequest();
        $request->setAccount($user->getAccount());
        $request->setShopOrderId($obj->getShopOrderId());
        $request->setBizAddr(AddressObject::fromArray($obj->getBizAddress()));
        $request->setOpenid($obj->getOpenId());
        $request->setOrderPath($obj->getOrderPath());
        $request->setGoodsList($obj->getGoodsList());
        $request->setOrderPrice($obj->getOrderPrice());

        $result = $this->client->request($request);
        // 保存这个退货ID
        $obj->setReturnId($result['return_id']);
    }

    #[Async]
    public function postRemove(DeliveryReturnOrder $obj): void
    {
        try {
            $request = new UnbindRequest();
            $request->setReturnId($obj->getReturnId());
            $this->client->request($request);
        } catch (\Throwable $exception) {
            $this->logger->error('退货组件删除ReturnOrder并同步删除远程时失败', [
                'returnOrder' => $obj,
                'exception' => $exception,
            ]);
        }
    }
}
