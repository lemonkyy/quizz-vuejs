<?php

namespace App\Api\Processor\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;
use App\Api\Dto\FriendRequest\MeAcceptDto;
use App\Entity\User;
use App\Exception\ValidationException;

class MeAcceptProcessor implements ProcessorInterface
{
    private int $maxFriends;

    public function __construct(ParameterBagInterface $params, private FriendRequestRepository $friendRequestRepository, private EntityManagerInterface $entityManager, private Security $security)
    {
        $this->maxFriends = $params->get('app.max_friends');
    }

    /**
     * @param MeAcceptDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): FriendRequest
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        $friendRequest = $this->friendRequestRepository->find($uriVariables['id']);

        $sender = $friendRequest->getSender();
        $receiver = $friendRequest->getReceiver();
        
        if (!$friendRequest || $receiver !== $user) {
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

        $friendRequest->setAcceptedAt(new \DateTimeImmutable());
        
        if (count($sender->getFriends()) >= $this->maxFriends) {
            throw new ValidationException('ERR_SENDER_MAX_FRIENDS_REACHED', 'Sender has reached the maximum number of friends', 400);
        }
        
        if (count($receiver->getFriends()) >= $this->maxFriends) {
            throw new ValidationException('ERR_RECEIVER_MAX_FRIENDS_REACHED', 'Receiver has reached the maximum number of friends', 400);
        }
        
        $sender->addFriend($receiver);
        $receiver->addFriend($sender);

        $this->entityManager->flush();

        return $friendRequest;
    }
}
