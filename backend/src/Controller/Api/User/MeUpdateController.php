<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ValidateUsernameService;

class MeUpdateController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager, ValidateUsernameService $validateUsernameService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['newUsername'])) {
            $error = $validateUsernameService->validate($data['newUsername'], $user ? $user->getId() : null);
    
            if ($error) {
                return new JsonResponse(['code' => $error['code'], 'error' => $error['message']], 400);
            }

            $user->setUsername($data['newUsername']);
        }

        $entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Username updated', 'username' => $user->getUsername()]);
    }
}
