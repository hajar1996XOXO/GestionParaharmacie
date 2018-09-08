<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Produit::class);
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
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

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param string|null $term
     */
    public function getWithSearchQueryBuilder(?string $term): QueryBuilder   //conbines search and pagination, returns QueryBuilder object
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.categorie', 'c'); //join table categorie
        if ($term) {
            $qb->andWhere('p.nom LIKE :term OR c.titre LIKE :term OR p.marque LIKE :term ')
                ->setParameter('term', '%' . $term . '%')
            ;
        }
        return $qb;
    }


    /**
     * @param   string|null $term
     * @return Produit[]
     */
    public function findAllWithCategorie(?string $term)  //just for search ,returns array of users
    {
        $qb = $this->createQueryBuilder('p')
                 ->innerJoin('p.categorie', 'c'); //join table categorie
        if ($term) {
            $qb->andWhere('c.titre LIKE :term ')
                ->setParameter('term', '%' . $term . '%')
            ;
        }
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param   string|null $term
     * @return Produit[]
     */
    public function findAllQuery()
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }






}
