<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CountryTaxRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryTaxRepository::class)]
#[ORM\Table(name: "country_taxes")]
#[ORM\UniqueConstraint(name: "country_taxes_uni_idx_code", columns: ["code"])]
class CountryTax
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $code;

    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

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
}
