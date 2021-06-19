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
        $missing = $posts->findLatestMissing();
        $petSitting = $posts->findLatestPetSittingAnnouncement();
        return $this->render('page/home.html.twig', [
            'missing' => $missing,
            'petSitting' => $petSitting
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
