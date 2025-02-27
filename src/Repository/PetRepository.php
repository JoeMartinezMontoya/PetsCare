<?php

namespace App\Repository;

use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
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
    public function findLostByTags($species , $tags): array
    {
        $db = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM pet p 
                LEFT JOIN pet_tags pt on p.id = pt.pet_id 
                WHERE pt.tags_id IN (:tags)
                AND p.species = :species    
                AND p.is_missing = 1";
        $query = $db->prepare($sql);
        foreach ($tags as $tag) {
            $query->bindParam(':tags', $tag, ParameterType::INTEGER);
        }
        $query->bindParam(':species', $species, ParameterType::INTEGER);
        $query->executeQuery();
        return $query->fetchAll();
    }
}
