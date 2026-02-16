<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CountryTax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CountryTax|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryTax|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method CountryTax[]    findAll()
 * @method CountryTax[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<CountryTax>
 */
class CountryTaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryTax::class);
    }

    public function findByCode(string $countryCode): ?CountryTax
    {
        /** @var CountryTax|null $countryTax */
        $countryTax = $this->findOneBy([
            'code' => strtolower($countryCode),
        ]);

        return $countryTax;
    }
}
