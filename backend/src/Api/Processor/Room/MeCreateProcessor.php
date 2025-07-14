<?php

namespace App\Api\Processor\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Room;
use App\Entity\User;
use App\Service\RoomMembershipService;
use App\Api\Dto\Room\CreateDto;
use App\Exception\ValidationException;

class MeCreateProcessor implements ProcessorInterface
{
    public function __construct(private RoomMembershipService $roomMembershipService, private Security $security)
    {
    }

    /**
     * @param CreateDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Room
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        $isPublic = $data->isPublic;

        $room = $this->roomMembershipService->handleUserCreatingRoom($user, $isPublic);

        return $room;
    }
}
