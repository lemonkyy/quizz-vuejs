<?php

namespace App\Api\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Api\Dto\User\RemoveFriendDto;
use App\Exception\ValidationException;

class RemoveFriendProcessor implements ProcessorInterface
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    /**
     * @param RemoveFriendDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        
        $friendToRemoveId = $uriVariables['id'];

        $friendToRemove = $this->userRepository->find($friendToRemoveId);

        if (!$friendToRemove) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not found', 404);
        }

        if (!$user->getFriends()->contains($friendToRemove)) {
            throw new ValidationException('ERR_NOT_FRIENDS', 'You are not friends with this user', 400);
        }

        $user->removeFriend($friendToRemove);
        $friendToRemove->removeFriend($user);

        $this->entityManager->flush();

        return;
    }
}
