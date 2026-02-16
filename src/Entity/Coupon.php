<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\CouponTypeEnum;
use App\Repository\CouponRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ORM\Table(name: "coupons")]
#[ORM\UniqueConstraint(name: "coupons_uni_idx_code", columns: ["code"])]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $code;

    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::STRING, enumType: CouponTypeEnum::class)]
    private CouponTypeEnum $type;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): CouponTypeEnum
    {
        return $this->type;
    }

    public function setType(CouponTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }
}
