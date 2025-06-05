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
    $start = (clone $date)->setTime((int)$heure->format('H'), (int)$heure->format('i'));
    $end = (clone $start)->modify("+$duree minutes");

    $qb = $this->createQueryBuilder('r');

    $qb->where('r.date = :date')
       ->andWhere(
           $qb->expr()->orX(
               $qb->expr()->between('r.heure', ':start', ':end'),
               $qb->expr()->andX(
                   'r.heure <= :start',
                   'DATE_ADD(r.heure, r.duree, \'minute\') > :start'
               )
           )
       );

    $qb->setParameter('date', $date); // <-- objet DateTime, pas string
    $qb->setParameter('start', $start->format('H:i:s'));
    $qb->setParameter('end', $end->format('H:i:s'));

    $result = $qb->getQuery()->getResult();

    return count($result) === 0;
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
