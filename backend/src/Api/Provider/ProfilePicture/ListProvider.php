<?php

namespace App\Api\Provider\ProfilePicture;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ProfilePictureRepository;
use App\Entity\ProfilePicture;

class ListProvider implements ProviderInterface
{
    public function __construct(private ProfilePictureRepository $profilePictureRepository)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === ProfilePicture::class && $operationName === 'get_profile_pictures';
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        return $this->profilePictureRepository->findAll();
    }
}