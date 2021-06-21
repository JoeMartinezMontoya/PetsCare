<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(PostRepository $posts): Response
    {
        $petSitting = $posts->findLatest(0);
        $petMissing = $posts->findLatest(1);
        $petToAdopt = $posts->findLatest(2);
        $petFound = $posts->findLatest(3);
        return $this->render('page/home.html.twig', [
            'current_menu' => 'home',
            'petSitting' => $petSitting,
            'petMissing' => $petMissing,
            'petToAdopt' => $petToAdopt,
            'petFound' => $petFound
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('page/about.html.twig', [
            'current_menu' => 'about'
        ]);
    }
}
