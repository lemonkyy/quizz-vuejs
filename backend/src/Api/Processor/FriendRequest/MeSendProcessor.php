<?php

namespace App\Api\Processor\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;
use App\Api\Dto\FriendRequest\SendDto;
use App\Entity\User;
use App\Exception\ValidationException;

class MeSendProcessor implements ProcessorInterface
{
    private int $maxSentFriendRequests;
    private int $maxReceivedFriendRequests;

    public function __construct(ParameterBagInterface $params, private UserRepository $userRepository, private FriendRequestRepository $friendRequestRepository, private EntityManagerInterface $entityManager, private Security $security)
    {
        $this->maxSentFriendRequests = $params->get('app.max_sent_friend_requests');
        $this->maxReceivedFriendRequests = $params->get('app.max_received_friend_requests');
    }

    /**
     * @param SendDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): FriendRequest
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        
        $receiverId = $uriVariables['id'];


        if ($receiverId === $user->getId()->__toString()) {
            throw new ValidationException('ERR_CANNOT_SEND_TO_SELF', 'Cannot send friend request to yourself', 400);
        }

        $receiver = $this->userRepository->find($receiverId);
        if (!$receiver) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'Receiver user not found', 404);
        }

        $existingRequest = $this->friendRequestRepository->findOneBy(['sender' => $user, 'receiver' => $receiver]);
        if ($existingRequest) {
            if ($existingRequest->getAcceptedAt() === null && $existingRequest->getDeniedAt() === null && $existingRequest->getRevokedAt() === null) {
                throw new ValidationException('ERR_FRIEND_REQUEST_ALREADY_SENT', 'Friend request already sent', 400);
            } else if ($existingRequest->getAcceptedAt() !== null) {
                throw new ValidationException('ERR_ALREADY_FRIENDS', 'You are already friends with this user', 400);
            }
        }

        $existingRequest = $this->friendRequestRepository->findOneBy(['sender' => $receiver, 'receiver' => $user]);
        if ($existingRequest) {
            if ($existingRequest->getAcceptedAt() === null && $existingRequest->getDeniedAt() === null && $existingRequest->getRevokedAt() === null) {
                throw new ValidationException('ERR_FRIEND_REQUEST_ALREADY_RECEIVED', 'You have a pending friend request from this user', 400);
            } else if ($existingRequest->getAcceptedAt() !== null) {
                throw new ValidationException('ERR_ALREADY_FRIENDS', 'You are already friends with this user', 400);
            }
        }

        if (count($user->getSentFriendRequests()) >= $this->maxSentFriendRequests) {
            throw new ValidationException('ERR_MAX_SENT_FRIEND_REQUESTS_REACHED', 'You have reached the maximum number of sent friend requests', 400);
        }

        if (count($receiver->getReceivedFriendRequests()) >= $this->maxReceivedFriendRequests) {
            throw new ValidationException('ERR_MAX_RECEIVED_FRIEND_REQUESTS_REACHED', 'The receiver has reached the maximum number of received friend requests', 400);
        }

        $friendRequest = new FriendRequest();
        $friendRequest->setSender($user);
        $friendRequest->setReceiver($receiver);

        $this->entityManager->persist($friendRequest);
        $this->entityManager->flush();

        return $friendRequest;
    }
}
