<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Model\InvoiceFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /** @return Invoice[] */
    public function findByFilter(InvoiceFilter $f, ?Company $companyFilter): array
    {
        $qb = $this->createQueryBuilder('i')
            ->orderBy('i.invoiceDate', 'DESC');

        if ($f->dateFrom) {
            $qb->andWhere('i.invoiceDate >= :from')
                ->setParameter('from', $f->dateFrom->setTime(0, 0, 0));
        }
        if ($f->dateTo) {
            // inklusiv bis Tagesende
            $to = $f->dateTo->setTime(23, 59, 59);
            $qb->andWhere('i.invoiceDate <= :to')
                ->setParameter('to', $to);
        }
        if ($f->totalMin !== null && $f->totalMin !== '') {
            $qb->andWhere('i.total >= :tmin')
                ->setParameter('tmin', $f->totalMin);
        }
        if ($f->totalMax !== null && $f->totalMax !== '') {
            $qb->andWhere('i.total <= :tmax')
                ->setParameter('tmax', $f->totalMax);
        }
        if($companyFilter) {
            $qb->andWhere('i.company = :company')
                ->setParameter('company', $companyFilter);
        }

        return $qb->getQuery()->getResult();
    }

}
