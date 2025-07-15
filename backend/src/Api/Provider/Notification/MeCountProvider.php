<?php

namespace App\Api\Provider\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\FriendRequestRepository;
use App\Repository\InvitationRepository;
use Symfony\Bundle\SecurityBundle\Security;

use App\Api\Dto\Notification\NotificationCountOutputDto;

class MeCountProvider implements ProviderInterface
{
    public function __construct(private Security $security, private FriendRequestRepository $friendRequestRepository, private InvitationRepository $invitationRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): NotificationCountOutputDto
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        $friendRequestCount = $this->friendRequestRepository->countActiveForUser($user);
        $invitationCount = $this->invitationRepository->countActiveForUser($user);

        return new NotificationCountOutputDto($friendRequestCount + $invitationCount);
    }
}
