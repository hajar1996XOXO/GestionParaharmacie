<?php

namespace App\Repository;

use App\Entity\ProduitCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProduitCart|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitCart|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitCart[]    findAll()
 * @method ProduitCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitCartRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProduitCart::class);
    }

//    /**
//     * @return ProduitCart[] Returns an array of ProduitCart objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
        * @return ProduitCart[] Returns an array of ProduitCart objects
     */
    public function findOneByClient($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.client = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

}
