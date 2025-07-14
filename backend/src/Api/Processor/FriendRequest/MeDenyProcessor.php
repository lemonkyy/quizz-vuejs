<?php

namespace App\Api\Processor\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;
use App\Entity\User;
use App\Api\Dto\FriendRequest\MeDenyDto;
use App\Exception\ValidationException;

class MeDenyProcessor implements ProcessorInterface
{
    public function __construct(private FriendRequestRepository $friendRequestRepository, private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    /**
     * @param MeDenyDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): FriendRequest
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        
        $friendRequest = $this->friendRequestRepository->find($uriVariables['id']);

        if (!$friendRequest || $friendRequest->getReceiver() !== $user) {
            throw new ValidationException('ERR_FRIEND_REQUEST_NOT_FOUND', 'Friend request not found', 404);
        }

        if ($friendRequest->getAcceptedAt() !== null) {
            throw new ValidationException('ERR_FRIEND_REQUEST_ALREADY_ACCEPTED', 'Friend request already accepted', 400);
        }

        if ($friendRequest->getDeniedAt() !== null) {
            throw new ValidationException('ERR_FRIEND_REQUEST_ALREADY_DENIED', 'Friend request already denied', 400);
        }

        if ($friendRequest->getRevokedAt() !== null) {
            throw new ValidationException('ERR_FRIEND_REQUEST_REVOKED', 'Friend request has been revoked by sender', 400);
        }

        $friendRequest->setDeniedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $friendRequest;
    }
}
