<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Entity\ProfilePicture;
use App\Repository\ProfilePictureRepository;
use App\Service\JWTCookieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ValidateUsernameService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

//update user
class MeUpdateController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager, ValidateUsernameService $validateUsernameService, JWTCookieService $cookieService, JWTTokenManagerInterface $jwtManager, ProfilePictureRepository $profilePictureRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['newUsername'])) {
            $error = $validateUsernameService->validate($data['newUsername'], $user ? $user->getId() : null);
    
            if ($error) {
                return new JsonResponse(['code' => $error['code'], 'error' => $error['message']], 400);
            }

            $user->setUsername($data['newUsername']);
        }

        if (isset($data['newProfilePictureId'])) {

            if (empty($data['newProfilePictureId'])) {
                return new JsonResponse(['code' => "ERR_NULL_PROFILE_PICTURE", 'error' => "User's profile picture cannot be set to null."], 400);
            }

            $profilePicture = $profilePictureRepository->find($data['newProfilePictureId']);

            if (!$profilePicture) {
                return new JsonResponse(['code' => 'ERR_PROFILE_PICTURE_NOT_FOUND', 'error' => 'Profile picture not found.'], 400);
            }

            $user->setProfilePicture($profilePicture);
        }

        if (!empty($data['clearTotpSecret'])) {
            $user->setTotpSecret(null);
        }

        $entityManager->flush();
        
        $jwtToken = $jwtManager->create($user);
        
        $response = new JsonResponse([
            'code' => 'SUCCESS', 
            'message' => 'User updated', 
            'username' => $user->getUsername(),
            'profilePicture' => [
                'id' => $user->getProfilePicture()->getId(),
                'fileName' => $user->getProfilePicture()->getFileName()
            ]
        ]);

        [$cookieHp, $cookieS] = $cookieService->createCookies($jwtToken);
        $response->headers->setCookie($cookieHp);
        $response->headers->setCookie($cookieS);

        return $response;
    }
}
