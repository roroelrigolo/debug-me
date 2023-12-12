<?php

namespace App\Repository;

use App\Entity\StatutTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutTicket>
 *
 * @method StatutTicket|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutTicket|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutTicket[]    findAll()
 * @method StatutTicket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutTicket::class);
    }

//    /**
//     * @return StatutTicket[] Returns an array of StatutTicket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatutTicket
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
