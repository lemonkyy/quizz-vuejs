<?php

namespace App\Controller\Api\User;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Component\HttpFoundation\Request;

//returns an array of users whose username match 
class SearchController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(UserRepository $userRepository, Request $request): JsonResponse
    {
        $username = $request->query->get('username');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        if (!$username) {
            return $this->json(['code' => 'ERROR', 'message' => 'Username is required'], 400);
        }

        $users = $userRepository->findByUsernamePaginated($username, $page, $limit);
        return $this->json(['code' => 'SUCCESS', 'users' => $users], 200, [], ['groups' => ['user:read']]);
    }
}
