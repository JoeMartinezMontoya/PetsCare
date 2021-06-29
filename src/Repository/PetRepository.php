<?php

namespace App\Repository;

use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pet[]    findAll()
 * @method Pet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    /**
     * @return Pet[]|null Returns an array of pet matching with Tags
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findLostByTags($tags): array
    {
        $db = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM pet p 
                LEFT JOIN pet_tags pt on p.id = pt.pet_id 
                WHERE pt.tags_id IN (:tags)
                AND p.is_missing = 1";
        $query = $db->prepare($sql);
        foreach ($tags as $tag) {
            $query->bindParam(':tags', $tag, ParameterType::INTEGER);
        }
        $query->executeQuery();
        return $query->fetchAll();
    }

    // /**
    //  * @return Pet[] Returns an array of Pet objects
    //  */
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
    public function findOneBySomeField($value): ?Pet
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
