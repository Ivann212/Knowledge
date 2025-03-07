<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\Formations;
use App\Entity\Lessons;
use App\Entity\Theme;

/**
 * Admin Dashboard controller for managing the admin interface of the application.
 * 
 * This controller provides the configuration for the admin dashboard, including
 * the title, menu items, and route for the dashboard.
 */
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    /**
     * Renders the admin dashboard page.
     * 
     * @return Response The rendered dashboard page.
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * Configures the dashboard settings, such as the title.
     * 
     * @return Dashboard The dashboard configuration.
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Knowledge'); // Title of the admin dashboard
    }

    /**
     * Configures the menu items for the admin panel.
     * This includes links to the dashboard and CRUD pages for entities.
     * 
     * @return iterable A list of menu items for the admin panel.
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'); // Link to the dashboard page
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class); // Link to the User CRUD page
        yield MenuItem::linkToCrud('Formations', 'fas fa-book', Formations::class); // Link to the Formations CRUD page
        yield MenuItem::linkToCrud('Leçons', 'fas fa-chalkboard-teacher', Lessons::class); // Link to the Lessons CRUD page
        yield MenuItem::linkToCrud('Thèmes', 'fas fa-layer-group', Theme::class); // Link to the Theme CRUD page
    }
}
