<?php

namespace App\Api\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProfilePictureRepository;
use App\Service\JWTCookieService;
use App\Service\ValidateUsernameService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Api\Dto\User\UpdateDto;

use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MeUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private ValidateUsernameService $validateUsernameService, 
        private ProfilePictureRepository $profilePictureRepository,
        private Security $security,
        private JWTTokenManagerInterface $jwtManager,
        private JWTCookieService $cookieService,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     *
     * @param UpdateDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ValidationException('ERR_USER_NOT_FOUND', 'User not authenticated.');
        }

        if (isset($data->newUsername)) {
            $error = $this->validateUsernameService->validate($data->newUsername, $user->getId());
    
            if ($error) {
                throw new ValidationException($error['code'], $error['message']);
            }

            $user->setUsername($data->newUsername);
        }

        if (isset($data->newProfilePictureId)) {

            if (empty($data->newProfilePictureId)) {
                throw new ValidationException('ERR_NULL_PROFILE_PICTURE', 'User\'s profile picture cannot be set to null.');
            }

            $profilePicture = $this->profilePictureRepository->find($data->newProfilePictureId);

            if (!$profilePicture) {
                throw new NotFoundHttpException('Profile picture not found.');
            }

            $user->setProfilePicture($profilePicture);
        }

        if (!empty($data->clearTotpSecret)) {
            $user->setTotpSecret(null);
        }

        $this->entityManager->flush();

        $jwtToken = $this->jwtManager->create($user);
        
        $response = new JsonResponse([
            'code' => 'SUCCESS', 
            'message' => 'User updated', 
            'username' => $user->getUsername(),
            'profilePicture' => [
                'id' => $user->getProfilePicture()->getId(),
                'fileName' => $user->getProfilePicture()->getFileName()
            ]
        ]);

        [$cookieHp, $cookieS] = $this->cookieService->createCookies($jwtToken);
        $response->headers->setCookie($cookieHp);
        $response->headers->setCookie($cookieS);

        return $response;
    }
}
