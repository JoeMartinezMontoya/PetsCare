<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
        $qb = $this->createQueryBuilder('p');
        $pictures = $qb
            ->select('p')
            ->where(
                $qb->expr()->in(
                    'p.id',
                    $this->createQueryBuilder('p2')
                        ->select('MAX(p2.id)')
                        ->where('p2.post IN (:posts)')
                        ->groupBy('p2.post')
                        ->getDQL()
                )
            )
            ->getQuery()
            ->setParameter('posts', $posts)
            ->getResult();

        $pictures = array_reduce($pictures, static function (array $acc, Picture $picture) {
            $acc[$picture->getPost()->getId()] = $picture;
            return $acc;
        }, []);
        return new ArrayCollection($pictures);
    }
}
