<?php

namespace WechatMiniProgramDeliveryReturnBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatMiniProgramDeliveryReturnBundle\Exception\ReturnOrderNotFoundException;

/**
 * @internal
 */
#[CoversClass(ReturnOrderNotFoundException::class)]
final class ReturnOrderNotFoundExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsInstanceOfRuntimeException(): void
    {
        $exception = new ReturnOrderNotFoundException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Return order not found with ID: 123';
        $exception = new ReturnOrderNotFoundException($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Return order not found';
        $code = 404;
        $exception = new ReturnOrderNotFoundException($message, $code);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previousException = new \Exception('Previous error');
        $exception = new ReturnOrderNotFoundException('Current error', 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }
}
