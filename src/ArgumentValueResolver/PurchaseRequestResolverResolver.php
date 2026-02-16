<?php

declare(strict_types=1);

namespace App\ArgumentValueResolver;

use App\ArgumentValueResolver\Tools\RequestValidator;
use App\Dto\Request\PurchaseRequest;
use App\Exception\RequestFormatException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

#[AsTargetedValueResolver('purchaseRequestResolver')]
readonly class PurchaseRequestResolverResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private RequestValidator $requestValidator,
    ) {
    }

    /**
     * @return iterable<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if ($argumentType !== PurchaseRequest::class) {
            return [];
        }

        try {
            $calculatePriceRequest = $this->serializer->deserialize(
                data: $request->getContent(),
                type: PurchaseRequest::class,
                format: 'json',
            );
        } catch (\Exception) {
            throw new RequestFormatException();
        }

        $this->validate($calculatePriceRequest);

        return [$calculatePriceRequest];
    }

    private function validate(PurchaseRequest $trigger): void
    {
        $errorMsg = $this->requestValidator->getErrorMsg(value: $trigger);
        if (null !== $errorMsg) {
            throw new RequestFormatException(userInterfaceMessage: $errorMsg);
        }
    }
}
