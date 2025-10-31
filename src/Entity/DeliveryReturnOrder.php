<?php

namespace WechatMiniProgramDeliveryReturnBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;
use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;

/**
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: DeliveryReturnOrderRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_delivery_return_order', options: ['comment' => '退货单'])]
class DeliveryReturnOrder implements ApiArrayInterface, \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    // #[ORM\ManyToOne]
    // private ?Account $account = null;

    #[ORM\Column(length: 64, unique: true, options: ['comment' => '商家内部系统使用的退货编号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $shopOrderId = null;

    /** @var array<string, mixed> */
    #[ORM\Column(options: ['comment' => '商家退货地址'])]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'array')]
    private array $bizAddress = [];

    /** @var array<string, mixed> */
    #[ORM\Column(options: ['comment' => '用户购物时的收货地址'])]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'array')]
    private array $userAddress = [];

    #[ORM\Column(length: 64, options: ['comment' => '退货用户的openid'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $openId = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '退货订单在商家小程序的path'])]
    #[Assert\Length(max: 255)]
    private ?string $orderPath = null;

    /** @var array<int, array<string, mixed>>|null */
    #[ORM\Column(nullable: true, options: ['comment' => '退货商品list'])]
    #[Assert\Type(type: 'array')]
    private ?array $goodsList = null;

    #[ORM\Column(options: ['comment' => '退货订单的价格（单位：分）'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private int $orderPrice;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '退货ID'])]
    #[Assert\Length(max: 50)]
    private ?string $returnId = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '运单号'])]
    #[Assert\Length(max: 60)]
    private ?string $waybillId = null;

    #[ORM\Column(nullable: true, enumType: DeliveryReturnStatus::class, options: ['comment' => '退货状态'])]
    #[Assert\Choice(callback: [DeliveryReturnStatus::class, 'cases'])]
    private ?DeliveryReturnStatus $status = null;

    #[ORM\Column(nullable: true, enumType: DeliveryReturnOrderStatus::class, options: ['comment' => '运单状态'])]
    #[Assert\Choice(callback: [DeliveryReturnOrderStatus::class, 'cases'])]
    private ?DeliveryReturnOrderStatus $orderStatus = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '运力公司名称'])]
    #[Assert\Length(max: 50)]
    private ?string $deliveryName = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '运力公司编码'])]
    #[Assert\Length(max: 20)]
    private ?string $deliveryId = null;

    // public function getAccount(): ?Account
    // {
    //     return $this->account;
    // }

    // public function setAccount(?Account $account): static
    // {
    //     $this->account = $account;

    //     return $this;
    // }

    public function getShopOrderId(): ?string
    {
        return $this->shopOrderId;
    }

    public function setShopOrderId(string $shopOrderId): void
    {
        $this->shopOrderId = $shopOrderId;
    }

    /**
     * @return array<string, mixed>
     */
    public function getBizAddress(): array
    {
        return $this->bizAddress;
    }

    /**
     * @param array<string, mixed> $bizAddress
     */
    public function setBizAddress(array $bizAddress): void
    {
        $this->bizAddress = $bizAddress;
    }

    /**
     * @return array<string, mixed>
     */
    public function getUserAddress(): array
    {
        return $this->userAddress;
    }

    /**
     * @param array<string, mixed> $userAddress
     */
    public function setUserAddress(array $userAddress): void
    {
        $this->userAddress = $userAddress;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): void
    {
        $this->openId = $openId;
    }

    public function getOrderPath(): ?string
    {
        return $this->orderPath;
    }

    public function setOrderPath(?string $orderPath): void
    {
        $this->orderPath = $orderPath;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getGoodsList(): ?array
    {
        return $this->goodsList;
    }

    /**
     * @param array<int, array<string, mixed>>|null $goodsList
     */
    public function setGoodsList(?array $goodsList): void
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

    public function getReturnId(): ?string
    {
        return $this->returnId;
    }

    public function setReturnId(?string $returnId): void
    {
        $this->returnId = $returnId;
    }

    public function getWaybillId(): ?string
    {
        return $this->waybillId;
    }

    public function setWaybillId(?string $waybillId): void
    {
        $this->waybillId = $waybillId;
    }

    public function getOrderStatus(): ?DeliveryReturnOrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?DeliveryReturnOrderStatus $orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    public function getDeliveryName(): ?string
    {
        return $this->deliveryName;
    }

    public function setDeliveryName(?string $deliveryName): void
    {
        $this->deliveryName = $deliveryName;
    }

    public function getDeliveryId(): ?string
    {
        return $this->deliveryId;
    }

    public function setDeliveryId(?string $deliveryId): void
    {
        $this->deliveryId = $deliveryId;
    }

    public function getStatus(): ?DeliveryReturnStatus
    {
        return $this->status;
    }

    public function setStatus(?DeliveryReturnStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array<string, mixed>
     */
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

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
