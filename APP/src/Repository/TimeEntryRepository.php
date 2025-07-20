<?php

namespace App\Repository;

use App\Entity\TimeEntry;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeEntry>
 *
 * @method TimeEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeEntry[]    findAll()
 * @method TimeEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
    }

    /**
     * Find all time entries for a given user, ordered by date descending
     *
     * @param User $user
     * @return TimeEntry[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.date', 'DESC')
            ->addOrderBy('t.startTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find entries within a date range for reporting or timesheet aggregation
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @return TimeEntry[]
     */
    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.date BETWEEN :start AND :end')
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->orderBy('t.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Persist and flush a TimeEntry
     *
     * @param TimeEntry $timeEntry
     * @param bool $flush
     * @return void
     */
    public function save(TimeEntry $timeEntry, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $em->persist($timeEntry);
        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Remove and flush a TimeEntry
     *
     * @param TimeEntry $timeEntry
     * @param bool $flush
     * @return void
     */
    public function remove(TimeEntry $timeEntry, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $em->remove($timeEntry);
        if ($flush) {
            $em->flush();
        }
    }
}
