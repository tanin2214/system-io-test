<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Coupon>
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function findByCode(string $couponCode): ?Coupon
    {
        /** @var Coupon|null $coupon */
        $coupon = $this->findOneBy([
            'code' => $couponCode,
        ]);

        return $coupon;
    }
}
