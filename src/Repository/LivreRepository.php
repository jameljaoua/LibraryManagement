<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }
public function search(?string $titre, ?string $auteurs, ?string $categorie)
{
    $qb = $this->createQueryBuilder('l')
        ->leftJoin('l.auteurs', 'a')
        ->leftJoin('l.categorie', 'c')
        ->addSelect('a', 'c');

    if ($titre) {
        $qb->andWhere('l.titre LIKE :titre')
           ->setParameter('titre', '%' . $titre . '%');
    }

    if ($auteurs) {
        $qb->andWhere("CONCAT(a.prenom, CONCAT(' ', a.nom)) LIKE :auteurs
        OR a.prenom LIKE :auteurs
        OR a.nom LIKE :auteurs
")
           ->setParameter('auteurs', '%' . $auteurs . '%');
    }

    if ($categorie) {
        $qb->andWhere('c.designation LIKE :categorie')
           ->setParameter('categorie', '%' . $categorie . '%');
    }

    return $qb->getQuery()->getResult();
}

//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
