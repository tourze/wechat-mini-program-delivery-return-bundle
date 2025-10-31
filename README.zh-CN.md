# wechat-mini-program-delivery-return-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-delivery-return-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/php-monorepo.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tourze/php-monorepo.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/php-monorepo/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-delivery-return-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-delivery-return-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](LICENSE)

用于管理微信小程序配送退货订单的 Symfony Bundle。该包提供了实体、服务和控制台命令，
用于处理微信小程序生态系统中的退货订单。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [依赖项](#依赖项)
- [快速开始](#快速开始)
  - [1. 启用 Bundle](#1-启用-bundle)
  - [2. 配置数据库](#2-配置数据库)
  - [3. 配置服务](#3-配置服务)
  - [4. 基本使用](#4-基本使用)
- [核心组件](#核心组件)
  - [实体](#实体)
  - [服务](#服务)
  - [命令](#命令)
  - [事件订阅者](#事件订阅者)
  - [仓储](#仓储)
  - [枚举](#枚举)
  - [请求对象](#请求对象)
- [配置](#配置)
- [高级用法](#高级用法)
  - [退货订单管理](#退货订单管理)
  - [事件处理](#事件处理)
  - [自定义扩展](#自定义扩展)
- [控制台命令](#控制台命令)
- [API 参考](#api-参考)
- [测试](#测试)
- [贡献](#贡献)
- [安全](#安全)
- [更新日志](#更新日志)
- [许可证](#许可证)

## 功能特性

- **实体管理**：完整的配送退货订单实体结构
- **服务层**：用于与微信 API 同步退货订单状态的服务
- **控制台命令**：手动和自动退货订单同步命令
- **事件处理**：用于退货订单处理的事件订阅者
- **仓储模式**：用于高效数据访问的自定义仓储
- **枚举支持**：用于退货订单状态的类型安全枚举
- **API 集成**：用于微信 API 通信的请求/响应对象

## 安装

```bash
composer require tourze/wechat-mini-program-delivery-return-bundle
```

## 依赖项

此包需要：

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM
- WeChat Mini Program Bundle

## 快速开始

### 1. 启用 Bundle

将 Bundle 添加到您的 `config/bundles.php`：

```php
<?php

return [
    // ... 其他 bundles
    WechatMiniProgramDeliveryReturnBundle\WechatMiniProgramDeliveryReturnBundle::class => ['all' => true],
];
```

### 2. 创建退货订单

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

// 创建新的退货订单
$returnOrder = new DeliveryReturnOrder();
$returnOrder->setShopOrderId('ORDER_123456');
$returnOrder->setOpenId('user_openid');
$returnOrder->setOrderPrice(9999); // 价格以分为单位
$returnOrder->setBizAddress([
    'province' => '广东',
    'city' => '深圳',
    'area' => '南山区',
    'street' => '腾讯大厦'
]);
$returnOrder->setUserAddress([
    'province' => '北京',
    'city' => '北京',
    'area' => '朝阳区',
    'street' => '用户地址'
]);

// 保存订单
$entityManager->persist($returnOrder);
$entityManager->flush();
```

### 3. 同步退货订单状态

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

// 注入服务
$deliveryReturnService = $container->get(DeliveryReturnService::class);

// 同步特定的退货订单
$deliveryReturnService->syncReturnOrder($returnOrder);
```

## 控制台命令

### 同步单个退货订单

通过商家订单 ID 同步单个退货订单：

```bash
php bin/console wechat-delivery-return:sync-single-return-order ORDER_123456
```

**参数：**
- `shopOrderId`：要同步的商家订单 ID

### 同步有效退货订单

自动同步所有有效的退货订单（每 15 分钟通过 cron 运行一次）：

```bash
php bin/console wechat-delivery-return:sync-valid-return-orders
```

此命令：
- 查找最近 15 天内创建的所有非取消退货订单
  （可通过 `WECHAT_DELIVERY_RETURN_SYNC_RETURN_ORDER_DAY_NUM` 环境变量配置）
- 为每个订单分派异步同步命令
- 每 15 分钟通过 cron 任务自动运行

## 高级用法

### 事件处理

该包提供用于自定义处理的事件订阅者：

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Event\ReturnOrderSyncedEvent;

// 监听退货订单同步事件
$eventDispatcher->addListener(ReturnOrderSyncedEvent::class, function (ReturnOrderSyncedEvent $event) {
    $returnOrder = $event->getReturnOrder();
    // 处理已同步的退货订单
});
```

### 自定义仓储方法

访问用于高级查询的自定义仓储方法：

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;

// 根据状态获取退货订单
$repository = $entityManager->getRepository(DeliveryReturnOrder::class);
$pendingOrders = $repository->findByStatus(DeliveryReturnStatus::Waiting);

// 获取日期范围内的退货订单
$recentOrders = $repository->findRecentOrders(15); // 最近 15 天
```

## 配置

### 环境变量

配置以下环境变量：

```bash
# 查找退货订单的天数（默认：15）
WECHAT_DELIVERY_RETURN_SYNC_RETURN_ORDER_DAY_NUM=15
```

## 服务

该包注册以下服务：

- `DeliveryReturnService`：退货订单操作的主要服务
- `DeliveryReturnOrderRepository`：退货订单数据访问的仓储
- 用于手动和自动同步的控制台命令

## API 集成

该包为微信 API 集成提供请求/响应对象：

### 请求对象

- `AddRequest`：用于创建新的退货订单
- `QueryStatusRequest`：用于查询退货订单状态
- `UnbindRequest`：用于解绑退货订单
- `AddressObject`：用于处理地址信息

### 响应处理

服务自动处理 API 响应并更新本地实体：

- 退货订单状态
- 运单号
- 快递公司信息
- 订单状态更新

## 数据库架构

该包创建以下数据库表：

```sql
-- 表：wechat_mini_program_delivery_return_order
-- 包含字段：
-- - id (雪花 ID)
-- - shop_order_id (唯一商家订单标识符)
-- - account_id (微信账户引用)
-- - biz_address (商家地址 JSON 字段)
-- - user_address (用户地址 JSON 字段)
-- - open_id (微信用户标识符)
-- - order_path (小程序订单路径)
-- - goods_list (商品信息 JSON 字段)
-- - order_price (价格，以分为单位)
-- - return_id (微信退货 ID)
-- - waybill_id (物流运单号)
-- - status (退货状态枚举)
-- - order_status (订单状态枚举)
-- - delivery_name (物流公司名称)
-- - delivery_id (物流公司 ID)
-- - create_time, update_time (时间戳)
```

## 状态枚举

### DeliveryReturnStatus
- 表示来自微信 API 的退货订单状态

### DeliveryReturnOrderStatus
- 表示物流订单状态
- `Cancelled`：订单已取消

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

## 许可证

MIT 许可证。请查看 [License File](LICENSE) 了解更多信息。
