<?php

namespace App\Api\Provider\Invitation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\InvitationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Invitation;
use App\Api\Dto\Invitation\ListSentDto;

class MeListSentProvider implements ProviderInterface
{
    public function __construct(private InvitationRepository $invitationRepository, private Security $security)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Invitation::class && $operationName === 'api_invitation_sent';
    }

    /**
     * @param ListSentDto $data
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();
        $targetUserId = $context['request']->query->get('user_id');

        $criteria = [
            'invitedBy' => $user,
            'acceptedAt' => null,
            'deniedAt' => null,
            'revokedAt' => null
        ];

        if ($targetUserId) {
            $criteria['invitedUser'] = $targetUserId;
        }

        return $this->invitationRepository->findBy($criteria);
    }
}