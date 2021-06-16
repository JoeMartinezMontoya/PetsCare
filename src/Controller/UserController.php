<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}-{id}", name="user_profile", requirements={"slug" : "[a-z0-9\-]*"})
     */
    public function index(User $user, string $slug): Response
    {
        //Redirection si quelqu'un essaie de rentrer un autre slug
        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user_profile', [
                'id' => $user->getId(),
                'slug' => $user->getSlug()
            ], 301);
        }
        return $this->render('user/index.html.twig');
    }
}
