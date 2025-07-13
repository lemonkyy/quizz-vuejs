<?php

namespace App\Api\Processor\FriendRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\FriendRequest;
use App\Entity\User;
use App\Api\Dto\FriendRequest\MeDenyDto;

class MeDenyProcessor implements ProcessorInterface
{
    public function __construct(private FriendRequestRepository $friendRequestRepository, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param MeDenyDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $friendRequest = $this->friendRequestRepository->find($data->id);

        if (!$friendRequest || $friendRequest->getReceiver() !== $user) {
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

        $friendRequest->setDeniedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Friend request denied'], 200);
    }
}