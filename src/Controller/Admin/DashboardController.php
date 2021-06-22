<?php

namespace App\Controller\Admin;

use App\Entity\Pet;
use App\Entity\Post;
use App\Entity\Tags;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PetsCare')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('PetsCare','fa fa-cat', 'home');
        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('User', 'fas fa-users', User::class);
        yield MenuItem::section('Pets');
        yield MenuItem::linkToCrud('Pets', 'fas fa-paw', Pet::class);
        yield MenuItem::linkToCrud('Tags', 'fas fa-tags', Tags::class);
        yield MenuItem::section('Posts');
        yield MenuItem::linkToCrud('Posts', 'fas fa-mail-bulk', Post::class)
            ->setDefaultSort(['created_at' => 'DESC']);
    }
}
