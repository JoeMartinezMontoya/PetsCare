<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class AppFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i <= 50; $i++) {
            $user = new User();
            $post = new Post();
            $post
                ->setAuthor($user->getId())
                ->setCreatedAt(new DateTime())
                ->setTitle('Test : ' . $i)
                ->setCategory(random_int(0,1))
                ->setContent('Text test numéro : ' . $i);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
