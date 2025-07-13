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
use App\Api\Dto\Invitation\MeCancelDto;

class MeCancelProcessor implements ProcessorInterface
{
    public function __construct(private InvitationRepository $invitationRepository, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param MeCancelDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');
        $invitation = $this->invitationRepository->find($data->id);

        if (!$invitation || $invitation->getInvitedBy() !== $user) {
            return new JsonResponse(['code' => 'ERR_INVITATION_NOT_FOUND', 'error' => 'Invitation not found or not allowed'], 404);
        }

        $invitation->setRevokedAt(new \DateTimeImmutable());

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return new JsonResponse(['code' => 'SUCCESS', 'message' => 'Invitation revoked'], 200);
    }
}