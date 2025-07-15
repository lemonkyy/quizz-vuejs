<?php

namespace App\Api\Provider\Notification;

use ApiPlatform\Metadata\Operation;
use App\Api\Paginator\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class MeListProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private NotificationRepository $notificationRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        //no automatic apiplatform pagination cause am using a native query
        $page = $this->requestStack->getCurrentRequest()->query->get('page');
        $limit = $this->requestStack->getCurrentRequest()->query->get('limit');

        return $this->notificationRepository->getNotificationsPaginated($user, $page, $limit);
    }
}
