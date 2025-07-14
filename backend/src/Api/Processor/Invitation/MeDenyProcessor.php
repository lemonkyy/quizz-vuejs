<?php

namespace App\Api\Processor\Invitation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InvitationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Invitation;
use App\Entity\User;
use App\Api\Dto\Invitation\MeDenyDto;
use App\Exception\ValidationException;

class MeDenyProcessor implements ProcessorInterface
{
    public function __construct(private InvitationRepository $invitationRepository, private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    /**
     * @param MeDenyDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Invitation
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }
        $invitation = $this->invitationRepository->find($uriVariables['id']);

        if (!$invitation || $invitation->getInvitedUser() !== $user) {
            throw new ValidationException('ERR_INVITATION_NOT_FOUND', 'Invitation not found', 404);
        }

        $invitation->setDeniedAt(new \DateTimeImmutable());

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return $invitation;
    }
}
