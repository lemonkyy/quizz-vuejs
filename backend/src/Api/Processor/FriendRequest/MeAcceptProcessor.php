<?php

namespace App\Api\Processor\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;
use App\Api\Dto\FriendRequest\MeAcceptDto;

class MeAcceptProcessor implements ProcessorInterface
{
    private int $maxFriends;

    public function __construct(ParameterBagInterface $params, private FriendRequestRepository $friendRequestRepository, private EntityManagerInterface $entityManager)
    {
        $this->maxFriends = $params->get('app.max_friends');
    }

    /**
     * @param MeAcceptDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $friendRequest = $this->friendRequestRepository->find($data->id);

        $sender = $friendRequest->getSender();
        $receiver = $friendRequest->getReceiver();
        
        if (!$friendRequest || $receiver !== $user) {
            return new JsonResponse(['code' => 'ERR_FRIEND_REQUEST_NOT_FOUND', 'error' => 'Friend request not found'], 404);
        }

        if ($friendRequest->getAcceptedAt() !== null) {
            return new JsonResponse(['code' => 'ERR_FRIEND_REQUEST_ALREADY_ACCEPTED', 'error' => 'Friend request already accepted'], 400);
        }

        if ($friendRequest->getDeniedAt() !== null) {
            return new JsonResponse(['code' => 'ERR_FRIEND_REQUEST_ALREADY_DENIED', 'error' => 'Friend request already denied'], 400);
        }

        if ($friendRequest->getRevokedAt() !== null) {
            return new JsonResponse(['code' => 'ERR_FRIEND_REQUEST_REVOKED', 'error' => 'Friend request has been revoked by sender'], 400);
        }

        $friendRequest->setAcceptedAt(new \DateTimeImmutable());
        
        if (count($sender->getFriends()) >= $this->maxFriends) {
            return new JsonResponse(['code' => 'ERR_SENDER_MAX_FRIENDS_REACHED', 'error' => 'Sender has reached the maximum number of friends'], 400);
        }
        
        if (count($receiver->getFriends()) >= $this->maxFriends) {
            return new JsonResponse(['code' => 'ERR_RECEIVER_MAX_FRIENDS_REACHED', 'error' => 'Receiver has reached the maximum number of friends'], 400);
        }
        
        $sender->addFriend($receiver);
        $receiver->addFriend($sender);

        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Friend request accepted'], 200);
    }
}