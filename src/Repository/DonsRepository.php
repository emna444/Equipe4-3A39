<?php

namespace App\Repository;

use App\Entity\Dons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Dons>
 *
 * @method Dons|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dons|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dons[]    findAll()
 * @method Dons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dons::class);
    }


    public function findEntitiesByString($str)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT d
                FROM App\Entity\Dons d
                WHERE d.title LIKE :str'
            )
            ->setParameter('str', '%' . $str . '%')
            ->getResult();
    }

    public function paginationQuery()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.id', 'ASC')
            ->getQuery()
        ;
    }
    

//    /**
//     * @return Dons[] Returns an array of Dons objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Dons
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
