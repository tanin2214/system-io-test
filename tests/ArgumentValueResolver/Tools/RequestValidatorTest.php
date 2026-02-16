<?php

declare(strict_types=1);

namespace App\Tests\ArgumentValueResolver\Tools;

use App\ArgumentValueResolver\Tools\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidatorTest extends TestCase
{
    public function testGetErrorMsgReturnsNullWhenNoViolations(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->with('value')
            ->willReturn(new ConstraintViolationList())
        ;

        $requestValidator = new RequestValidator($validator);

        self::assertNull($requestValidator->getErrorMsg('value'));
    }

    public function testGetErrorMsgReturnsFormattedMessageWhenViolationsExist(): void
    {
        $violation1 = new ConstraintViolation('Error 1', null, [], null, 'field1', null);

        $violation2 = new ConstraintViolation('Error 2', null, [], null, 'field2', null);

        $list = new ConstraintViolationList([$violation1, $violation2]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->with('value')
            ->willReturn($list)
        ;

        $requestValidator = new RequestValidator($validator);

        $message = $requestValidator->getErrorMsg('value');

        self::assertSame('Error 1 (поле: field1); Error 2 (поле: field2)', $message);
    }
}
