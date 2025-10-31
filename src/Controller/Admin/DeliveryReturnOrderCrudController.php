<?php

declare(strict_types=1);

namespace WechatMiniProgramDeliveryReturnBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;

/**
 * 微信小程序退货单管理控制器
 */
#[AdminCrud(routePath: '/wechat-mini-program-delivery-return/delivery-return-order', routeName: 'wechat_mini_program_delivery_return_delivery_return_order')]
final class DeliveryReturnOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryReturnOrder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('退货单')
            ->setEntityLabelInPlural('退货单管理')
            ->setPageTitle('index', '微信小程序退货单管理')
            ->setSearchFields(['shopOrderId', 'openId', 'returnId', 'waybillId'])
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('shopOrderId', '商家订单号')
                ->setRequired(true)
                ->setHelp('商家内部系统使用的退货编号')
                ->setMaxLength(64),

            TextField::new('openId', '用户OpenID')
                ->setRequired(true)
                ->setHelp('退货用户的openid')
                ->setMaxLength(64),

            ArrayField::new('bizAddress', '商家退货地址')
                ->setRequired(true)
                ->setHelp('商家退货地址信息')
                ->onlyWhenCreating(),

            ArrayField::new('userAddress', '用户收货地址')
                ->setRequired(true)
                ->setHelp('用户购物时的收货地址')
                ->onlyWhenCreating(),

            TextField::new('orderPath', '订单路径')
                ->setRequired(false)
                ->setHelp('退货订单在商家小程序的path')
                ->setMaxLength(255),

            ArrayField::new('goodsList', '退货商品列表')
                ->setRequired(false)
                ->setHelp('退货商品信息列表')
                ->onlyWhenCreating(),

            IntegerField::new('orderPrice', '订单价格(分)')
                ->setRequired(true)
                ->setHelp('退货订单的价格，单位：分')
                ->setFormTypeOptions(['attr' => ['min' => 0]]),

            TextField::new('returnId', '退货ID')
                ->setRequired(false)
                ->setHelp('微信小程序返回的退货ID')
                ->setMaxLength(50),

            TextField::new('waybillId', '运单号')
                ->setRequired(false)
                ->setHelp('物流运单号')
                ->setMaxLength(60),

            ChoiceField::new('status', '退货状态')
                ->setRequired(false)
                ->setHelp('退货单的状态')
                ->setChoices(array_combine(
                    array_map(fn ($case) => $case->getLabel(), DeliveryReturnStatus::cases()),
                    DeliveryReturnStatus::cases()
                )),

            ChoiceField::new('orderStatus', '运单状态')
                ->setRequired(false)
                ->setHelp('物流运单的状态')
                ->setChoices(array_combine(
                    array_map(fn ($case) => $case->getLabel(), DeliveryReturnOrderStatus::cases()),
                    DeliveryReturnOrderStatus::cases()
                )),

            TextField::new('deliveryName', '运力公司名称')
                ->setRequired(false)
                ->setHelp('承运的物流公司名称')
                ->setMaxLength(50),

            TextField::new('deliveryId', '运力公司编码')
                ->setRequired(false)
                ->setHelp('物流公司的编码')
                ->setMaxLength(20),

            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updateTime', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('shopOrderId', '商家订单号'))
            ->add(TextFilter::new('openId', '用户OpenID'))
            ->add(TextFilter::new('returnId', '退货ID'))
            ->add(ChoiceFilter::new('status', '退货状态')
                ->setChoices(array_combine(
                    array_map(fn ($case) => $case->getLabel(), DeliveryReturnStatus::cases()),
                    DeliveryReturnStatus::cases()
                )))
            ->add(ChoiceFilter::new('orderStatus', '运单状态')
                ->setChoices(array_combine(
                    array_map(fn ($case) => $case->getLabel(), DeliveryReturnOrderStatus::cases()),
                    DeliveryReturnOrderStatus::cases()
                )))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
