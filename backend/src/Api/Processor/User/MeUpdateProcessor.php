<?php

namespace App\Api\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProfilePictureRepository;
use App\Service\JWTCookieService;
use App\Service\ValidateUsernameService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Entity\ProfilePicture;
use App\Api\Dto\User\UpdateDto;

class MeUpdateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private ValidateUsernameService $validateUsernameService, private JWTCookieService $cookieService, private JWTTokenManagerInterface $jwtManager, private ProfilePictureRepository $profilePictureRepository)
    {
    }

    /**
     * @param UpdateDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $user = $context['request']->attributes->get('user');

        if (isset($data->newUsername)) {
            $error = $this->validateUsernameService->validate($data->newUsername, $user ? $user->getId() : null);
    
            if ($error) {
                return new JsonResponse(['code' => $error['code'], 'error' => $error['message']], 400);
            }

            $user->setUsername($data->newUsername);
        }

        if (isset($data->newProfilePictureId)) {

            if (empty($data->newProfilePictureId)) {
                return new JsonResponse(['code' => "ERR_NULL_PROFILE_PICTURE", 'error' => "User's profile picture cannot be set to null."], 400);
            }

            $profilePicture = $this->profilePictureRepository->find($data->newProfilePictureId);

            if (!$profilePicture) {
                return new JsonResponse(['code' => 'ERR_PROFILE_PICTURE_NOT_FOUND', 'error' => 'Profile picture not found.'], 400);
            }

            $user->setProfilePicture($profilePicture);
        }

        if (!empty($data->clearTotpSecret)) {
            $error = $this->validateUsernameService->validate($requestData['newUsername'], $user ? $user->getId() : null);
    
            if ($error) {
                return new JsonResponse(['code' => $error['code'], 'error' => $error['message']], 400);
            }

            $user->setUsername($requestData['newUsername']);
        }

        if (isset($requestData['newProfilePictureId'])) {

            if (empty($requestData['newProfilePictureId'])) {
                return new JsonResponse(['code' => "ERR_NULL_PROFILE_PICTURE", 'error' => "User's profile picture cannot be set to null."], 400);
            }

            $profilePicture = $this->profilePictureRepository->find($requestData['newProfilePictureId']);

            if (!$profilePicture) {
                return new JsonResponse(['code' => 'ERR_PROFILE_PICTURE_NOT_FOUND', 'error' => 'Profile picture not found.'], 400);
            }

            $user->setProfilePicture($profilePicture);
        }

        if (!empty($requestData['clearTotpSecret'])) {
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
