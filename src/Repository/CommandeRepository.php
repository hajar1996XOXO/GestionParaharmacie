<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * @return Commande[] Returns an array of Commande objects
   */

    public function findByExampleField()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.etat NOT LIKE :val')
            ->setParameter('val', 'Confirmée')
            ->getQuery()
            ->getResult()
        ;
    }



    /**
     * @return Commande[] Returns an array of Commande objects
     */

    public function findByExampleField2()   //confirmed commands
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.etat  LIKE :val')
            ->setParameter('val', 'Confirmée')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Commande
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
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
        $qb = $this->createQueryBuilder('comm')
            ->innerJoin('comm.client', 'client'); //join table client
             $qb->andWhere('comm.etat NOT LIKE :term ')  //only show commandes en attente
                ->setParameter('term', 'Confirmée');

        if ($term) {
            $qb->andWhere('client.prenom LIKE :term OR client.nom LIKE :term ')
                ->setParameter('term', '%' . $term . '%')
            ;
        }
        return $qb;
    }


    /**
     * @return Commande[] Returns an array of Commande objects
     */

    public function findCommandeByUser($val)
    {
        return $this->createQueryBuilder('com')
            ->innerJoin('com.client', 'client')
            ->andWhere('client.email  LIKE :val')
            ->setParameter('val', $val)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param   string|null $term
     * @return Commande[]
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
