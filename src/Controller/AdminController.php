<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\TagCrudController;
use App\Controller\Admin\ServiceCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'admin_user')]
    public function manageUsers(AdminUrlGenerator $adminUrlGenerator): Response
    {
        $url = $adminUrlGenerator->setController(UserCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    #[Route('/admin/tags', name: 'admin_tag')]
    public function manageTags(AdminUrlGenerator $adminUrlGenerator): Response
    {
        $url = $adminUrlGenerator->setController(TagCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    #[Route('/admin/services', name: 'admin_service')]
    public function manageServices(AdminUrlGenerator $adminUrlGenerator): Response
    {
        $url = $adminUrlGenerator->setController(ServiceCrudController::class)->generateUrl();
        return $this->redirect($url);
    }
}
