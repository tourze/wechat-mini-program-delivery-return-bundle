# wechat-mini-program-delivery-return-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-delivery-return-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/php-monorepo.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tourze/php-monorepo.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/php-monorepo/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-delivery-return-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-delivery-return-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-mini-program-delivery-return-bundle.svg?style=flat-square)](LICENSE)

A Symfony bundle for managing WeChat Mini Program delivery return orders. This bundle provides 
entities, services, and console commands for handling return orders within WeChat Mini Program 
ecosystem.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Dependencies](#dependencies)
- [Quick Start](#quick-start)
  - [1. Enable the Bundle](#1-enable-the-bundle)
  - [2. Configure Database](#2-configure-database)
  - [3. Configure Services](#3-configure-services)
  - [4. Basic Usage](#4-basic-usage)
- [Core Components](#core-components)
  - [Entities](#entities)
  - [Services](#services)
  - [Commands](#commands)
  - [Event Subscribers](#event-subscribers)
  - [Repositories](#repositories)
  - [Enums](#enums)
  - [Request Objects](#request-objects)
- [Configuration](#configuration)
- [Advanced Usage](#advanced-usage)
  - [Return Order Management](#return-order-management)
  - [Event Handling](#event-handling)
  - [Custom Extensions](#custom-extensions)
- [Console Commands](#console-commands)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security](#security)
- [Changelog](#changelog)
- [License](#license)

## Features

- **Entity Management**: Complete entity structure for delivery return orders
- **Service Layer**: Service for synchronizing return order status with WeChat API
- **Console Commands**: Commands for manual and automated return order synchronization
- **Event Handling**: Event subscribers for return order processing
- **Repository Pattern**: Custom repository for efficient data access
- **Enum Support**: Type-safe enums for return order statuses
- **API Integration**: Request/response objects for WeChat API communication

## Installation

```bash
composer require tourze/wechat-mini-program-delivery-return-bundle
```

## Dependencies

This bundle requires:

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM
- WeChat Mini Program Bundle

## Quick Start

### 1. Enable the Bundle

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    WechatMiniProgramDeliveryReturnBundle\WechatMiniProgramDeliveryReturnBundle::class => ['all' => true],
];
```

### 2. Create a Return Order

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;
use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

// Create a new return order
$returnOrder = new DeliveryReturnOrder();
$returnOrder->setShopOrderId('ORDER_123456');
$returnOrder->setOpenId('user_openid');
$returnOrder->setOrderPrice(9999); // Price in cents
$returnOrder->setBizAddress([
    'province' => 'Guangdong',
    'city' => 'Shenzhen',
    'area' => 'Nanshan',
    'street' => 'Tencent Building'
]);
$returnOrder->setUserAddress([
    'province' => 'Beijing',
    'city' => 'Beijing',
    'area' => 'Chaoyang',
    'street' => 'User Address'
]);

// Persist the order
$entityManager->persist($returnOrder);
$entityManager->flush();
```

### 3. Sync Return Order Status

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Service\DeliveryReturnService;

// Inject the service
$deliveryReturnService = $container->get(DeliveryReturnService::class);

// Sync a specific return order
$deliveryReturnService->syncReturnOrder($returnOrder);
```

## Console Commands

### Sync Single Return Order

Synchronize a single return order by shop order ID:

```bash
php bin/console wechat-delivery-return:sync-single-return-order ORDER_123456
```

**Parameters:**
- `shopOrderId`: The shop order ID to synchronize

### Sync Valid Return Orders

Automatically synchronize all valid return orders (runs every 15 minutes via cron):

```bash
php bin/console wechat-delivery-return:sync-valid-return-orders
```

This command:
- Finds all non-cancelled return orders created within the last 15 days 
  (configurable via `WECHAT_DELIVERY_RETURN_SYNC_RETURN_ORDER_DAY_NUM` environment variable)
- Dispatches asynchronous sync commands for each order
- Runs automatically every 15 minutes via cron job

## Advanced Usage

### Event Handling

The bundle provides event subscribers for custom processing:

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Event\ReturnOrderSyncedEvent;

// Listen for return order sync events
$eventDispatcher->addListener(ReturnOrderSyncedEvent::class, function (ReturnOrderSyncedEvent $event) {
    $returnOrder = $event->getReturnOrder();
    // Handle the synchronized return order
});
```

### Custom Repository Methods

Access custom repository methods for advanced queries:

```php
<?php

use WechatMiniProgramDeliveryReturnBundle\Repository\DeliveryReturnOrderRepository;

// Get return orders by status
$repository = $entityManager->getRepository(DeliveryReturnOrder::class);
$pendingOrders = $repository->findByStatus(DeliveryReturnStatus::Waiting);

// Get return orders within date range
$recentOrders = $repository->findRecentOrders(15); // Last 15 days
```

## Configuration

### Environment Variables

Configure the following environment variables:

```bash
# Number of days to look back for return orders (default: 15)
WECHAT_DELIVERY_RETURN_SYNC_RETURN_ORDER_DAY_NUM=15
```

## Services

The bundle registers the following services:

- `DeliveryReturnService`: Main service for return order operations
- `DeliveryReturnOrderRepository`: Repository for return order data access
- Console commands for manual and automated synchronization

## API Integration

The bundle provides request/response objects for WeChat API integration:

### Request Objects

- `AddRequest`: For creating new return orders
- `QueryStatusRequest`: For querying return order status
- `UnbindRequest`: For unbinding return orders
- `AddressObject`: For handling address information

### Response Handling

The service automatically handles API responses and updates local entities with:

- Return order status
- Waybill ID
- Delivery company information
- Order status updates

## Database Schema

The bundle creates the following database table:

```sql
-- Table: wechat_mini_program_delivery_return_order
-- Columns include:
-- - id (snowflake ID)
-- - shop_order_id (unique shop order identifier)
-- - account_id (WeChat account reference)
-- - biz_address (JSON field for business address)
-- - user_address (JSON field for user address)
-- - open_id (WeChat user identifier)
-- - order_path (mini program order path)
-- - goods_list (JSON field for goods information)
-- - order_price (price in cents)
-- - return_id (WeChat return ID)
-- - waybill_id (logistics waybill ID)
-- - status (return status enum)
-- - order_status (order status enum)
-- - delivery_name (logistics company name)
-- - delivery_id (logistics company ID)
-- - create_time, update_time (timestamps)
```

## Status Enums

### DeliveryReturnStatus
- Represents the return order status from WeChat API

### DeliveryReturnOrderStatus
- Represents the logistics order status
- `Cancelled`: Order has been cancelled

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.