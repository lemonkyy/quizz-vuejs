<?php

namespace App\Api\Processor\Invitation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InvitationRepository;
use App\Service\RoomMembershipService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Invitation;
use App\Entity\User;
use App\Api\Dto\Invitation\MeAcceptDto;

class MeAcceptProcessor implements ProcessorInterface
{
    public function __construct(private InvitationRepository $invitationRepository, private EntityManagerInterface $entityManager, private ParameterBagInterface $params, private RoomMembershipService $roomMembershipService)
    {
    }

    /**
     * @param MeAcceptDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $invitation = $this->invitationRepository->find($data->id);

        if (!$invitation || $invitation->getInvitedUser() !== $user) {
            return new JsonResponse(['code' => 'ERR_INVITATION_NOT_FOUND', 'error' => 'Invitation not found'], 404);
        }

        $expirationThreshold = $this->params->get('app.invite_expiration_threshold');
        $expirationDate = (clone $invitation->getInvitedAt())->modify('+' . $expirationThreshold);
        $now = new \DateTimeImmutable();

        if ($now > $expirationDate) {
            return new JsonResponse(['code' => 'ERR_INVITATION_EXPIRED', 'error' => 'Invitation has expired'], 400);
        }

        if ($invitation->getRevokedAt() !== null) {
            return new JsonResponse(['code' => 'ERR_INVITATION_REVOKED', 'error' => 'Invitation has been revoked'], 400);
        }

        $invitation->setAcceptedAt($now);
        $room = $invitation->getRoom();

        if ($room->getDeletedAt() !== null) {
            return new JsonResponse(['code' => 'ERR_ROOM_DELETED', 'error' => 'Room has been deleted'], 400);
        }

        $this->roomMembershipService->handleUserJoiningRoom($user, $room);
        
        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Invitation accepted'], 200);
    }
}