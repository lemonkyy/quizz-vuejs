<?php

namespace App\Controller\Api\FriendRequest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//accept friend invite
class MeAcceptController extends AbstractController
{
    private int $maxFriends;

    public function __construct(ParameterBagInterface $params)
    {
        $this->maxFriends = $params->get('app.max_friends');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $entityManager): Response
    {
        $friendRequest = $friendRequestRepository->find($id);

        if (!$friendRequest || $friendRequest->getReceiver() !== $user) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_NOT_FOUND', 'error' => 'Friend request not found'], 404);
        }

        if ($friendRequest->getAcceptedAt() !== null) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_ALREADY_ACCEPTED', 'error' => 'Friend request already accepted'], 400);
        }

        if ($friendRequest->getDeniedAt() !== null) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_ALREADY_DENIED', 'error' => 'Friend request already denied'], 400);
        }

        if ($friendRequest->getRevokedAt() !== null) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_REVOKED', 'error' => 'Friend request has been revoked by sender'], 400);
        }

        $friendRequest->setAcceptedAt(new \DateTimeImmutable());

        // Add each other to friends list
        $sender = $friendRequest->getSender();
        $receiver = $friendRequest->getReceiver();

        if (count($sender->getFriends()) >= $this->maxFriends) {
            return $this->json(['code' => 'ERR_SENDER_MAX_FRIENDS_REACHED', 'error' => 'Sender has reached the maximum number of friends'], 400);
        }

        if (count($receiver->getFriends()) >= $this->maxFriends) {
            return $this->json(['code' => 'ERR_RECEIVER_MAX_FRIENDS_REACHED', 'error' => 'Receiver has reached the maximum number of friends'], 400);
        }

        $sender->addFriend($receiver);
        $receiver->addFriend($sender);

        $entityManager->flush();

        return $this->json(['code' => 'SUCCESS', 'message' => 'Friend request accepted'], 200);
    }
}
