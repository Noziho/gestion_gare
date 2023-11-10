<?php

namespace App\Repository;

use App\Entity\TrainStation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrainStation>
 *
 * @method TrainStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainStation[]    findAll()
 * @method TrainStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainStation::class);
    }

//    /**
//     * @return TrainStation[] Returns an array of TrainStation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TrainStation
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
