<?php

namespace App\Api\Processor\Invitation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InvitationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Invitation;
use App\Entity\User;
use App\Api\Dto\Invitation\MeDenyDto;

class MeDenyProcessor implements ProcessorInterface
{
    public function __construct(private InvitationRepository $invitationRepository, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param MeDenyDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $invitation = $this->invitationRepository->find($data->id);

        if (!$invitation || $invitation->getInvitedUser() !== $user) {
            return new JsonResponse(['code' => 'ERR_INVITATION_NOT_FOUND', 'error' => 'Invitation not found'], 404);
        }

        $invitation->setDeniedAt(new \DateTimeImmutable());

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Invitation denied'], 200);
    }
}