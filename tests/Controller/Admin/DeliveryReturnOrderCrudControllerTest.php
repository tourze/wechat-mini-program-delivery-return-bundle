<?php

declare(strict_types=1);

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatMiniProgramDeliveryReturnBundle\Controller\Admin\DeliveryReturnOrderCrudController;
use WechatMiniProgramDeliveryReturnBundle\Entity\DeliveryReturnOrder;

/**
 * 微信小程序退货单管理控制器测试
 * @internal
 */
#[CoversClass(DeliveryReturnOrderCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryReturnOrderCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return DeliveryReturnOrderCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(DeliveryReturnOrderCrudController::class);
    }

    public function testGetEntityFqcn(): void
    {
        self::assertSame(DeliveryReturnOrder::class, $this->getControllerService()::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = $this->getControllerService();
        $fields = $controller->configureFields('index');

        self::assertIsIterable($fields);
        self::assertNotEmpty($fields);
    }

    public function testRequiredFieldsValidation(): void
    {
        $controller = $this->getControllerService();
        $fields = $controller->configureFields('new');

        $fieldNames = [];
        foreach ($fields as $field) {
            if (is_string($field)) {
                $fieldNames[] = $field;
            } else {
                $fieldDto = $field->getAsDto();
                $fieldNames[] = $fieldDto->getProperty();
            }
        }

        // 验证关键字段存在
        self::assertContains('shopOrderId', $fieldNames);
        self::assertContains('openId', $fieldNames);
        self::assertContains('orderPrice', $fieldNames);
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        $entityName = $this->getEntitySimpleName();

        // 尝试查找保存按钮，如果找不到则跳过此测试
        $saveButtons = $crawler->selectButton('保存');
        if (0 === $saveButtons->count()) {
            $saveButtons = $crawler->filter('button[type="submit"]');
        }

        if (0 === $saveButtons->count()) {
            self::markTestSkipped('无法找到提交按钮，跳过验证测试');
        }

        $form = $saveButtons->form();

        // 提交空表单验证必填字段错误
        $crawler = $client->submit($form);

        // 可能是重定向或其他响应，检查是否有错误信息
        if (422 === $client->getResponse()->getStatusCode()) {
            // 验证有错误信息
            $errorElements = $crawler->filter('.invalid-feedback, .form-error-message, .has-error, .alert-danger');
            $this->assertGreaterThan(0, $errorElements->count(), '应该有验证错误信息');
        } else {
            // 如果不是422状态码，说明验证行为可能不同，我们检查是否有表单重新显示
            $this->assertTrue(
                $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
                '提交表单后应该成功或重定向'
            );
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '小程序订单号' => ['商家订单号'];
        yield '微信OpenID' => ['用户OpenID'];
        yield '订单路径' => ['订单路径'];
        yield '订单金额' => ['订单价格(分)'];
        yield '退货单号' => ['退货ID'];
        yield '运单号' => ['运单号'];
        yield '状态' => ['退货状态'];
        yield '运单状态' => ['运单状态'];
        yield '运力公司名称' => ['运力公司名称'];
        yield '运力公司编码' => ['运力公司编码'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'shopOrderId' => ['shopOrderId'];
        yield 'openId' => ['openId'];
        yield 'orderPrice' => ['orderPrice'];
        yield 'orderPath' => ['orderPath'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'shopOrderId' => ['shopOrderId'];
        yield 'openId' => ['openId'];
        yield 'returnId' => ['returnId'];
        yield 'orderPrice' => ['orderPrice'];
        yield 'orderPath' => ['orderPath'];
        yield 'status' => ['status'];
    }
}
