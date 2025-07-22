<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\QuizzesCrudController;
use App\Controller\Admin\FriendRequestCrudController;
use App\Controller\Admin\RoomCrudController;
use App\Controller\Admin\InvitationCrudController;
use App\Controller\Admin\ProfilePictureCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect(
            $adminUrlGenerator->setController(UserCrudController::class)->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Quizup administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', UserCrudController::class);
        yield MenuItem::linkToCrud('Quizzes', 'fas fa-question', QuizzesCrudController::class);
        yield MenuItem::linkToCrud('Friend Requests', 'fas fa-user-friends', FriendRequestCrudController::class);
        yield MenuItem::linkToCrud('Rooms', 'fas fa-door-open', RoomCrudController::class);
        yield MenuItem::linkToCrud('Invitations', 'fas fa-envelope', InvitationCrudController::class);
        yield MenuItem::linkToCrud('Profile Pictures', 'fas fa-image', ProfilePictureCrudController::class);
    }
}
