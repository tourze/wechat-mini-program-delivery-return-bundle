<?php

namespace WechatMiniProgramDeliveryReturnBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Exception\ReturnOrderNotFoundException;
use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;
use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

#[AsCommand(name: self::NAME, description: '同步单个退货信息到本地')]
class SyncSingleReturnOrderCommand extends LockableCommand
{
    public const NAME = 'wechat-delivery-return:sync-single-return-order';

    public function __construct(
        private readonly DeliveryReturnOrderRepository $orderRepository,
        private readonly DeliveryReturnService $deliveryReturnService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('同步单个退货信息到本地')
            ->addArgument('shopOrderId', InputArgument::REQUIRED, '商家内部系统使用的退货编号')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $order = $this->orderRepository->findOneBy([
            'shopOrderId' => $input->getArgument('shopOrderId'),
        ]);
        if (null === $order) {
            throw new ReturnOrderNotFoundException('找不到退货单');
        }

        // Type assertion to ensure $order is DeliveryReturnOrder
        assert($order instanceof DeliveryReturnOrder);

        $this->deliveryReturnService->syncReturnOrder($order);

        return Command::SUCCESS;
    }
}
