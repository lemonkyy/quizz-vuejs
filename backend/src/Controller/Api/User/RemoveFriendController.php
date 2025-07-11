<?php

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class RemoveFriendController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $friendToRemove = $userRepository->find($id);

        if (!$friendToRemove) {
            return $this->json(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        if (!$user->getFriends()->contains($friendToRemove)) {
            return $this->json(['code' => 'ERR_NOT_FRIENDS', 'error' => 'You are not friends with this user'], 400);
        }

        $user->removeFriend($friendToRemove);
        $friendToRemove->removeFriend($user);

        $entityManager->flush();

        return $this->json(['code' => 'SUCCESS', 'message' => 'Friend removed successfully'], 200);
    }
}
