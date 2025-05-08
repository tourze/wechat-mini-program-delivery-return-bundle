<?php

namespace WechatMiniProgramDeliveryReturnBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;
use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;

#[ORM\Entity(repositoryClass: DeliveryReturnOrderRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_delivery_return_order', options: ['comment' => '退货单'])]
class DeliveryReturnOrder implements ApiArrayInterface
{
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    private ?Account $account = null;

    #[ORM\Column(length: 64, unique: true, options: ['comment' => '商家内部系统使用的退货编号'])]
    private ?string $shopOrderId = null;

    #[ORM\Column(options: ['comment' => '商家退货地址'])]
    private array $bizAddress = [];

    #[ORM\Column(options: ['comment' => '用户购物时的收货地址'])]
    private array $userAddress = [];

    #[ORM\Column(length: 64, options: ['comment' => '退货用户的openid'])]
    private ?string $openId = null;

    /**
     * @var string|null 如投保时已传入订单商品信息，则以投保时传入的为准
     */
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '退货订单在商家小程序的path'])]
    private ?string $orderPath = null;

    /**
     * @var array|null 一个元素为对象的数组,结构如下↓ 如投保时已传入订单商品信息，则以投保时传入的为准
     */
    #[ORM\Column(nullable: true, options: ['comment' => '退货商品list'])]
    private ?array $goodsList = null;

    #[ORM\Column(options: ['comment' => '退货订单的价格（单位：分）'])]
    private int $orderPrice;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '退货ID'])]
    private ?string $returnId = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '运单号'])]
    private ?string $waybillId = null;

    #[ORM\Column(nullable: true, enumType: DeliveryReturnStatus::class, options: ['comment' => '退货状态'])]
    private ?DeliveryReturnStatus $status = null;

    #[ORM\Column(nullable: true, enumType: DeliveryReturnOrderStatus::class, options: ['comment' => '运单状态'])]
    private ?DeliveryReturnOrderStatus $orderStatus = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '运力公司名称'])]
    private ?string $deliveryName = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '运力公司编码'])]
    private ?string $deliveryId = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getShopOrderId(): ?string
    {
        return $this->shopOrderId;
    }

    public function setShopOrderId(string $shopOrderId): static
    {
        $this->shopOrderId = $shopOrderId;

        return $this;
    }

    public function getBizAddress(): array
    {
        return $this->bizAddress;
    }

    public function setBizAddress(array $bizAddress): static
    {
        $this->bizAddress = $bizAddress;

        return $this;
    }

    public function getUserAddress(): array
    {
        return $this->userAddress;
    }

    public function setUserAddress(array $userAddress): static
    {
        $this->userAddress = $userAddress;

        return $this;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): static
    {
        $this->openId = $openId;

        return $this;
    }

    public function getOrderPath(): ?string
    {
        return $this->orderPath;
    }

    public function setOrderPath(?string $orderPath): static
    {
        $this->orderPath = $orderPath;

        return $this;
    }

    public function getGoodsList(): ?array
    {
        return $this->goodsList;
    }

    public function setGoodsList(?array $goodsList): static
    {
        $this->goodsList = $goodsList;

        return $this;
    }

    public function getOrderPrice(): ?int
    {
        return $this->orderPrice;
    }

    public function setOrderPrice(int $orderPrice): static
    {
        $this->orderPrice = $orderPrice;

        return $this;
    }

    public function getReturnId(): ?string
    {
        return $this->returnId;
    }

    public function setReturnId(?string $returnId): static
    {
        $this->returnId = $returnId;

        return $this;
    }

    public function getWaybillId(): ?string
    {
        return $this->waybillId;
    }

    public function setWaybillId(?string $waybillId): static
    {
        $this->waybillId = $waybillId;

        return $this;
    }

    public function getOrderStatus(): ?DeliveryReturnOrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?DeliveryReturnOrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    public function getDeliveryName(): ?string
    {
        return $this->deliveryName;
    }

    public function setDeliveryName(?string $deliveryName): static
    {
        $this->deliveryName = $deliveryName;

        return $this;
    }

    public function getDeliveryId(): ?string
    {
        return $this->deliveryId;
    }

    public function setDeliveryId(?string $deliveryId): static
    {
        $this->deliveryId = $deliveryId;

        return $this;
    }

    public function getStatus(): ?DeliveryReturnStatus
    {
        return $this->status;
    }

    public function setStatus(?DeliveryReturnStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'shopOrderId' => $this->getShopOrderId(),
            'bizAddress' => $this->getBizAddress(),
            'userAddress' => $this->getUserAddress(),
            'status' => $this->getStatus()?->toArray(),
            'orderStatus' => $this->getOrderStatus()?->toArray(),
            'returnId' => $this->getReturnId(),
            'deliveryName' => $this->getDeliveryName(),
            'deliveryId' => $this->getDeliveryId(),
        ];
    }
}
