<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\User;
use App\Api\Dto\User\MeListFriendsDto;

class MeListFriendsProvider implements ProviderInterface
{
    public function __construct(private UserRepository $userRepository, private Security $security)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class && $operationName === 'api_user_list_friends';
    }

    /**
     * @param MeListFriendsDto $data
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \Exception("User not found");
        }

        $page = $context['request']->query->getInt('page', 1);
        $limit = $context['request']->query->getInt('limit', 10);
        $username = $context['request']->query->get('username');

        $friendsData = $this->userRepository->findFriendsByUserIdPaginated(
            $user->getId()->__toString(),
            $page,
            $limit,
            $username
        );

        return $friendsData;
    }
}
