<?php

namespace WechatMiniProgramDeliveryReturnBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnOrderStatus;
use WechatMiniProgramDeliveryReturnBundle\Enum\DeliveryReturnStatus;

class DeliveryReturnOrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('zh_CN');

        for ($i = 1; $i <= 10; ++$i) {
            // 使用测试环境的 MockUserLoader，不需要创建真实用户
            $openId = $faker->regexify('[a-zA-Z0-9]{28}');

            $order = new DeliveryReturnOrder();
            $order->setShopOrderId('SHOP_ORDER_' . $faker->unique()->numberBetween(1000, 9999));
            $order->setBizAddress([
                'name' => $faker->name(),
                'mobile' => $faker->phoneNumber(),
                'country' => '中国',
                'province' => $faker->randomElement(['北京市', '上海市', '广东省', '浙江省', '江苏省']),
                'city' => $faker->city(),
                'area' => $faker->streetName(),
                'address' => $faker->address(),
            ]);
            $order->setUserAddress([
                'receiver_name' => $faker->name(),
                'detailed_address' => $faker->address(),
                'tel_number' => $faker->phoneNumber(),
                'country' => '中国',
                'province' => $faker->randomElement(['北京市', '上海市', '广东省', '浙江省', '江苏省']),
                'city' => $faker->city(),
                'town' => $faker->streetName(),
            ]);
            $order->setOpenId($openId);
            $order->setOrderPath('/pages/order/detail?id=' . $faker->numberBetween(1, 1000));
            $order->setGoodsList([
                [
                    'good_id' => $faker->numberBetween(1, 100),
                    'good_name' => $faker->word(),
                    'good_count' => $faker->numberBetween(1, 5),
                    'good_price' => $faker->numberBetween(100, 10000),
                ],
            ]);
            $order->setOrderPrice($faker->numberBetween(100, 50000));
            $order->setReturnId($faker->regexify('[0-9]{12}'));
            $order->setWaybillId($faker->regexify('[A-Z0-9]{10,15}'));

            $statusCases = DeliveryReturnStatus::cases();
            $randomStatus = $faker->randomElement($statusCases);
            assert($randomStatus instanceof DeliveryReturnStatus);
            $order->setStatus($randomStatus);

            $orderStatusCases = DeliveryReturnOrderStatus::cases();
            $randomOrderStatus = $faker->randomElement($orderStatusCases);
            assert($randomOrderStatus instanceof DeliveryReturnOrderStatus);
            $order->setOrderStatus($randomOrderStatus);

            $deliveryNames = ['顺丰速运', '中通快递', '韵达速递', '申通快递', '圆通速递'];
            $randomDeliveryName = $faker->randomElement($deliveryNames);
            assert(is_string($randomDeliveryName));
            $order->setDeliveryName($randomDeliveryName);

            $order->setDeliveryId($faker->regexify('[A-Z]{2}[0-9]{3}'));

            $manager->persist($order);
        }

        $manager->flush();
    }
}
