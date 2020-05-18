<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @param int $id Platform Id
     * @return Review[] Returns an array of Review objects by platform
     */
    public function findByPlatform(int $id)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->addSelect('g, p, ge')
            ->leftJoin('r.game', 'g')
            ->leftJoin('r.comments', 'c')
            ->leftJoin('g.platform', 'p')
            ->leftJoin('g.genre', 'ge')
            ->Where('p.id = :val')
            ->setParameter('val', $id)
            ->orderBy('r.createdAt', 'DESC')
        ;
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Review[] Returns an array of Review objects order by id DESC
     */
    public function findByOrderOfCreation()
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->addSelect('g, p, ge')
            ->leftJoin('r.game', 'g')
            ->leftJoin('r.comments', 'c')
            ->leftJoin('g.platform', 'p')
            ->leftJoin('g.genre', 'ge')
            ->orderBy('r.createdAt', 'DESC')
        ;
        return $qb->getQuery()->getResult();
    }
    /*
    public function findOneBySomeField($value): ?Review
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
