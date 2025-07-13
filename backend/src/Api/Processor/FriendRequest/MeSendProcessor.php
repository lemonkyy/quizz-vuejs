<?php

namespace App\Api\Processor\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;
use App\Api\Dto\FriendRequest\SendDto;

class MeSendProcessor implements ProcessorInterface
{
    private int $maxSentFriendRequests;
    private int $maxReceivedFriendRequests;

    public function __construct(ParameterBagInterface $params, private UserRepository $userRepository, private FriendRequestRepository $friendRequestRepository, private EntityManagerInterface $entityManager)
    {
        $this->maxSentFriendRequests = $params->get('app.max_sent_friend_requests');
        $this->maxReceivedFriendRequests = $params->get('app.max_received_friend_requests');
    }

    /**
     * @param SendDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $receiverId = $data->receiverId;

        if ($receiverId === $user->getId()->__toString()) {
            return new JsonResponse(['code' => 'ERR_CANNOT_SEND_TO_SELF', 'error' => 'Cannot send friend request to yourself'], 400);
        }

        $receiver = $this->userRepository->find($receiverId);
        if (!$receiver) {
            return new JsonResponse(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'Receiver user not found'], 404);
        }

        $existingRequest = $this->friendRequestRepository->findOneBy(['sender' => $user, 'receiver' => $receiver]);
        if ($existingRequest) {
            if ($existingRequest->getAcceptedAt() === null && $existingRequest->getDeniedAt() === null && $existingRequest->getRevokedAt() === null) {
                return new JsonResponse(['code' => 'ERR_FRIEND_REQUEST_ALREADY_SENT', 'error' => 'Friend request already sent'], 400);
            } else if ($existingRequest->getAcceptedAt() !== null) {
                return new JsonResponse(['code' => 'ERR_ALREADY_FRIENDS', 'error' => 'You are already friends with this user'], 400);
            }
        }

        $existingRequest = $this->friendRequestRepository->findOneBy(['sender' => $receiver, 'receiver' => $user]);
        if ($existingRequest) {
            if ($existingRequest->getAcceptedAt() === null && $existingRequest->getDeniedAt() === null && $existingRequest->getRevokedAt() === null) {
                return new JsonResponse(['code' => 'ERR_FRIEND_REQUEST_ALREADY_RECEIVED', 'error' => 'You have a pending friend request from this user'], 400);
            } else if ($existingRequest->getAcceptedAt() !== null) {
                return new JsonResponse(['code' => 'ERR_ALREADY_FRIENDS', 'error' => 'You are already friends with this user'], 400);
            }
        }

        if (count($user->getSentFriendRequests()) >= $this->maxSentFriendRequests) {
            return new JsonResponse(['code' => 'ERR_MAX_SENT_FRIEND_REQUESTS_REACHED', 'error' => 'You have reached the maximum number of sent friend requests'], 400);
        }

        if (count($receiver->getReceivedFriendRequests()) >= $this->maxReceivedFriendRequests) {
            return new JsonResponse(['code' => 'ERR_MAX_RECEIVED_FRIEND_REQUESTS_REACHED', 'error' => 'The receiver has reached the maximum number of received friend requests'], 400);
        }

        $friendRequest = new FriendRequest();
        $friendRequest->setSender($user);
        $friendRequest->setReceiver($receiver);

        $this->entityManager->persist($friendRequest);
        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Friend request sent'], 201);
    }
}