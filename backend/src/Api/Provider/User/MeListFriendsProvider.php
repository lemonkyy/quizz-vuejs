<?php

namespace App\Api\Provider\User;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\RequestStack;

class MeListFriendsProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private Security $security,
        private RequestStack $requestStack,
        private Pagination $pagination
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        [$page, , $limit] = $this->pagination->getPagination($operation, $context);
        $username = $this->requestStack->getCurrentRequest()->query->get('username');

        return new Paginator($this->userRepository->findFriendsByUserIdPaginated($user->getId()->__toString(), $page, $limit, $username));
    }
}
