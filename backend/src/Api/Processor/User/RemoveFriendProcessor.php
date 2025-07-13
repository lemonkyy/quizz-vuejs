<?php

namespace App\Api\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Api\Dto\User\RemoveFriendDto;

class RemoveFriendProcessor implements ProcessorInterface
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param RemoveFriendDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $friendToRemoveId = $data->friendId;

        $friendToRemove = $this->userRepository->find($friendToRemoveId);

        if (!$friendToRemove) {
            return new JsonResponse(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'User not found'], 404);
        }

        if (!$user->getFriends()->contains($friendToRemove)) {
            return new JsonResponse(['code' => 'ERR_NOT_FRIENDS', 'error' => 'You are not friends with this user'], 400);
        }

        $user->removeFriend($friendToRemove);
        $friendToRemove->removeFriend($user);

        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Friend removed successfully'], 200);
    }
}
