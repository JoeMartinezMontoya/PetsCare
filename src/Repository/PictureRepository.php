<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    /**
     * @param Post[] $posts
     * @return ArrayCollection
     */
    public function findForPosts(array $posts): ArrayCollection
    {
        $em = $this->getEntityManager();
        $sql = "SELECT p.id, p.image_name
                FROM picture p
                WHERE p.id IN(SELECT MAX(pp.picture_id) 
                              FROM post_picture pp 
                              WHERE pp.post_id IN(:posts) 
                              GROUP BY pp.post_id)";

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata(Picture::class, 'p');
        $rsm->addEntityResult(Picture::class, 'p');
        $rsm->addFieldResult('p', 'p.image_name', 'image_name');

        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter('posts', $posts);

        $pictures = $query->getResult();

        $pictures = array_reduce($pictures, static function (array $acc, Picture $picture) {
            foreach ($picture->getPosts() as $post) {
                $acc[$post->getId()] = $picture;
            }
            return $acc;
        }, []);
        return new ArrayCollection($pictures);
    }
}
