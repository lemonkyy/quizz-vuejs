<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route(path: '/api/user/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): Response
    {
        // This action will not be reached, as the security component handles the logout.
        throw new \Exception('This should not be reached!');
    }
}
