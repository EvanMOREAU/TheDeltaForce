<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les événements passés
     * 
     * @return Event[]
     */
    public function findPastEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateFin < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateFin', 'DESC')
            ->getQuery()
            ->getResult();
    }

/**
     * Trouve les événements futurs
     * 
     * @return Event[]
     */
    public function findUpcomingEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateFin >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
