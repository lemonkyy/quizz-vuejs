<?php

namespace App\Controller\Api\FriendRequest;

use App\Entity\FriendRequest;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//send a friend request to another user
class MeSendController extends AbstractController
{
    private int $maxSentFriendRequests;
    private int $maxReceivedFriendRequests;

    public function __construct(ParameterBagInterface $params)
    {
        $this->maxSentFriendRequests = $params->get('app.max_sent_friend_requests');
        $this->maxReceivedFriendRequests = $params->get('app.max_received_friend_requests');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, UserRepository $userRepository, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $receiverId = $id;

        if ($receiverId === $user->getId()->__toString()) {
            return $this->json(['code' => 'ERR_CANNOT_SEND_TO_SELF', 'error' => 'Cannot send friend request to yourself'], 400);
        }

        $receiver = $userRepository->find($receiverId);
        if (!$receiver) {
            return $this->json(['code' => 'ERR_USER_NOT_FOUND', 'error' => 'Receiver user not found'], 404);
        }

        $existingRequest = $friendRequestRepository->findOneBy(['sender' => $user, 'receiver' => $receiver]);
        if ($existingRequest) {
            if ($existingRequest->getAcceptedAt() === null && $existingRequest->getDeniedAt() === null && $existingRequest->getRevokedAt() === null) {
                return $this->json(['code' => 'ERR_FRIEND_REQUEST_ALREADY_SENT', 'error' => 'Friend request already sent'], 400);
            } else if ($existingRequest->getAcceptedAt() !== null) {
                return $this->json(['code' => 'ERR_ALREADY_FRIENDS', 'error' => 'You are already friends with this user'], 400);
            }
        }

        $existingRequest = $friendRequestRepository->findOneBy(['sender' => $receiver, 'receiver' => $user]);
        if ($existingRequest) {
            if ($existingRequest->getAcceptedAt() === null && $existingRequest->getDeniedAt() === null && $existingRequest->getRevokedAt() === null) {
                return $this->json(['code' => 'ERR_FRIEND_REQUEST_ALREADY_RECEIVED', 'error' => 'You have a pending friend request from this user'], 400);
            } else if ($existingRequest->getAcceptedAt() !== null) {
                return $this->json(['code' => 'ERR_ALREADY_FRIENDS', 'error' => 'You are already friends with this user'], 400);
            }
        }

        if (count($user->getSentFriendRequests()) >= $this->maxSentFriendRequests) {
            return $this->json(['code' => 'ERR_MAX_SENT_FRIEND_REQUESTS_REACHED', 'error' => 'You have reached the maximum number of sent friend requests'], 400);
        }

        if (count($receiver->getReceivedFriendRequests()) >= $this->maxReceivedFriendRequests) {
            return $this->json(['code' => 'ERR_MAX_RECEIVED_FRIEND_REQUESTS_REACHED', 'error' => 'The receiver has reached the maximum number of received friend requests'], 400);
        }

        $friendRequest = new FriendRequest();
        $friendRequest->setSender($user);
        $friendRequest->setReceiver($receiver);

        $entityManager->persist($friendRequest);
        $entityManager->flush();

        return $this->json(['code' => 'SUCCESS', 'message' => 'Friend request sent'], 201);
    }
}
