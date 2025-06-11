<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RendezVous>
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

public function isCreneauLibre(\DateTime $date, \DateTime $heure, int $duree): bool
{
    $qb = $this->createQueryBuilder('r');
    $qb->where('r.date = :date')
        ->andWhere('r.heure BETWEEN :start AND :end')
        ->setParameter('date', $date->format('Y-m-d'))
        ->setParameter('start', $heure->format('H:i:s'))
        ->setParameter('end', (clone $heure)->modify("+$duree minutes")->format('H:i:s'));

    return count($qb->getQuery()->getResult()) === 0;
}




    //    /**
    //     * @return RendezVous[] Returns an array of RendezVous objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RendezVous
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
} // <-- N’oublie pas cette accolade fermante !
