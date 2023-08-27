<?php

namespace App\Repository;

use App\Entity\CalendarPerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalendarPerson>
 *
 * @method CalendarPerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalendarPerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalendarPerson[]    findAll()
 * @method CalendarPerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarPersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarPerson::class);
    }

    //    /**
    //     * @return CalendarPerson[] Returns an array of CalendarPerson objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CalendarPerson
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
