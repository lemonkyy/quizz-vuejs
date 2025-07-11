<?php

namespace App\Controller\Api\FriendRequest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

//cancel friend invite
class MeCancelController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] $user, string $id, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $entityManager): Response
    {
        $friendRequest = $friendRequestRepository->find($id);

        if (!$friendRequest || $friendRequest->getSender() !== $user) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_NOT_FOUND', 'error' => 'Friend request not found or not allowed'], 404);
        }

        if ($friendRequest->getAcceptedAt() !== null) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_ALREADY_ACCEPTED', 'error' => 'Friend request already accepted'], 400);
        }

        if ($friendRequest->getDeniedAt() !== null) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_ALREADY_DENIED', 'error' => 'Friend request already denied'], 400);
        }

        if ($friendRequest->getRevokedAt() !== null) {
            return $this->json(['code' => 'ERR_FRIEND_REQUEST_ALREADY_REVOKED', 'error' => 'Friend request already revoked'], 400);
        }

        $friendRequest->setRevokedAt(new \DateTimeImmutable());
        $entityManager->flush();

        return $this->json(['code' => 'SUCCESS', 'message' => 'Friend request revoked'], 200);
    }
}
