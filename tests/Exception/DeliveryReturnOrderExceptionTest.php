<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatMiniProgramDeliveryReturnBundle\Exception\DeliveryReturnOrderException;

/**
 * @internal
 */
#[CoversClass(DeliveryReturnOrderException::class)]
final class DeliveryReturnOrderExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsInstanceOfRuntimeException(): void
    {
        $exception = new DeliveryReturnOrderException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'OpenId 不能为空';
        $exception = new DeliveryReturnOrderException($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = '找不到小程序用户';
        $code = 404;
        $exception = new DeliveryReturnOrderException($message, $code);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previousException = new \Exception('Previous error');
        $exception = new DeliveryReturnOrderException('Current error', 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }
}
