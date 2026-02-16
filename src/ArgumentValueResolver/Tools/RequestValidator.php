<?php

declare(strict_types=1);

namespace App\ArgumentValueResolver\Tools;

use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class RequestValidator
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function getErrorMsg(mixed $value): ?string
    {
        $violations = $this->validator->validate($value);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage() . ' (поле: ' . $violation->getPropertyPath() . ')';
            }

            return implode('; ', $errors);
        }

        return null;
    }
}
