<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findAllForUser(\App\Entity\User $user): array
    {
        // Beispiel: Kunden der Companies, auf die der User Zugriff hat
        return $this->createQueryBuilder('c')
            ->innerJoin('c.company', 'co')
            ->innerJoin('co.users', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->orderBy('c.lastname', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
