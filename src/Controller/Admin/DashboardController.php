<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render(view: 'admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(title: 'App Amichiamoci')
            ->setFaviconPath(path: 'assets/logos/favicon.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute(
            label: 'App', 
            icon: 'fa-solid fa-mobile',
            routeName: 'app_home',
        );
        yield MenuItem::linkToDashboard(
            label: 'Dashboard', 
            icon: 'fa fa-home',
        );
        yield MenuItem::linkToRoute(
            label: 'Utenti', 
            icon: 'fa-regular fa-user', 
            routeName: 'admin_user_index',
        );
    }
}
