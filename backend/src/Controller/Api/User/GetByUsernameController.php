<?php

namespace App\Controller\Api\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//get an user by its username
class GetByUsernameController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(Request $request, UserRepository $userRepository): JsonResponse
    {
        $username = $request->query->get('username');

        if (!$username) {
            return new JsonResponse(['code' => 'ERR_MISSING_USERNAME', 'error' => 'Username is required.'], 400);
        }

        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            return new JsonResponse(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        return $this->json(['code' => 'SUCCESS', 'user' => $user], 200, [], ['groups' => ['user:read']]);
    }
}
