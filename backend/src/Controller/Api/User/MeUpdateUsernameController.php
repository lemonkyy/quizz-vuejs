<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\ValidateUsernameService;

class MeUpdateUsernameController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, ParameterBagInterface $params, ValidateUsernameService $validateUsernameService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username'])) {
            return new JsonResponse(['error' => 'Invalid username'], 400);
        }

        $error = $validateUsernameService->validate($data['username'], $user ? $user->getId() : null);

        if ($error) {
            return new JsonResponse(['error' => $error], 400);
        }

        $user->setUsername($data['username']);

        $entityManager->flush();

        return new JsonResponse(['status' => 'Username updated', 'username' => $user->getUsername()]);
    }
}
