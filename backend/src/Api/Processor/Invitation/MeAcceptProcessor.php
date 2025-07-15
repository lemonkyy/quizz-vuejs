<?php

namespace App\Api\Processor\Invitation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InvitationRepository;
use App\Service\RoomMembershipService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Invitation;
use App\Entity\User;
use App\Api\Dto\Invitation\MeAcceptDto;
use App\Exception\ValidationException;

class MeAcceptProcessor implements ProcessorInterface
{
    public function __construct(private InvitationRepository $invitationRepository, private EntityManagerInterface $entityManager, private ParameterBagInterface $params, private RoomMembershipService $roomMembershipService, private Security $security)
    {
    }

    /**
     * @param MeAcceptDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Invitation
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        
        $invitation = $this->invitationRepository->find($uriVariables['id']);

        if (!$invitation || $invitation->getReceiver() !== $user) {
            throw new ValidationException('ERR_INVITATION_NOT_FOUND', 'Invitation not found', 404);
        }

        $expirationThreshold = $this->params->get('app.invite_expiration_threshold');
        $expirationDate = (clone $invitation->getSentAt())->modify('+' . $expirationThreshold);
        $now = new \DateTimeImmutable();

        if ($now > $expirationDate) {
            throw new ValidationException('ERR_INVITATION_EXPIRED', 'Invitation has expired', 400);
        }

        if ($invitation->getRevokedAt() !== null) {
            throw new ValidationException('ERR_INVITATION_REVOKED', 'Invitation has been revoked', 400);
        }

        $invitation->setAcceptedAt($now);
        $room = $invitation->getRoom();

        if ($room->getDeletedAt() !== null) {
            throw new ValidationException('ERR_ROOM_DELETED', 'Room has been deleted', 400);
        }

        $this->roomMembershipService->handleUserJoiningRoom($user, $room);
        
        $this->entityManager->flush();

        return $invitation;
    }
}
