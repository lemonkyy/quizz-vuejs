<?php

namespace App\Api\Provider\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class MeListProvider implements ProviderInterface
{
    private const PAGE_PARAMETER_NAME = 'page';
    private const ITEMS_PER_PAGE_PARAMETER_NAME = 'itemsPerPage';

    public function __construct(
        private readonly Security $security,
        private readonly NotificationRepository $notificationRepository,
        private readonly RequestStack $requestStack
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PaginatorInterface
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        $page = (int) $this->requestStack->getCurrentRequest()->query->get(self::PAGE_PARAMETER_NAME, 1);
        $itemsPerPage = (int) $this->requestStack->getCurrentRequest()->query->get(self::ITEMS_PER_PAGE_PARAMETER_NAME, 30);

        $notifications = $this->notificationRepository->getNotifications($user, $page, $itemsPerPage);
        $totalItems = $this->notificationRepository->countNotifications($user);

        return new \ApiPlatform\State\Pagination\Paginator($notifications, $page, $itemsPerPage, $totalItems);
    }
}
