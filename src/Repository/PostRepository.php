<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Post;
use App\Entity\PostSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    public function findLatest($category)
    {
        $posts = $this->createQueryBuilder('p')
            ->andWhere('p.category = :val')
            ->setParameter('val', $category)
            ->orderBy('p.created_at', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
        $this->hydratePicture($posts);
        return $posts;
    }

    /**
     * @param PostSearch $search
     * @param int $page
     * @param int|null $user
     * @return PaginationInterface
     */
    public function paginateAllVisible(PostSearch $search, int $page, int $user = null): PaginationInterface
    {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC');

        if (!is_null($search->getCategory())) {
            $query->andWhere('p.category = :val')
                ->setParameter('val', $search->getCategory());
        }

        if ($search->getCreatedAt()) {
            $query->andWhere('p.created_at > :date')
                ->setParameter('date', $search->getCreatedAt());
        }

        if ($user !== null) {
            $query->andWhere('p.author = :user')
                ->setParameter('user', $user);
        }

        $posts = $this->paginator->paginate($query->getQuery(), $page, 12);

        $this->hydratePicture($posts);
        return $posts;
    }

    private function hydratePicture($posts): void
    {
        if (method_exists($posts, 'getItems')) {
            $posts = $posts->getItems();
        }
        $pictures = $this->getEntityManager()->getRepository(Picture::class)->findForPosts($posts);
        foreach ($posts as $post) {
            /*** @var Post $post */
            if ($pictures->containsKey($post->getId())) {
                $post->setPicture($pictures->get($post->getId()));
            }
        }
    }

    /**
     * Used by the Owner of the missingPet when it is found, retrieve all posts about the missingPet and delete them
     * @param $pet
     * @return int|mixed|string
     */
    public function findLostAndFoundPostsAbout($pet)
    {
        $categories = [1, 3];
        return $this->createQueryBuilder('p')
            ->delete()
            ->andWhere('p.category IN (:category)')
            ->setParameter('category', $categories)
            ->andWhere('p.missingPet = :pet')
            ->setParameter('pet', $pet)
            ->getQuery()
            ->getResult();
    }
}
