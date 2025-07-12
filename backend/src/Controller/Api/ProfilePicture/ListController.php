<?php

namespace App\Controller\Api\ProfilePicture;

use App\Repository\ProfilePictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ListController extends AbstractController
{
    public function __invoke(ProfilePictureRepository $profilePictureRepository): Response
    {
        $profilePictures = $profilePictureRepository->findAll();
        return $this->json(['code' => 'SUCCESS', 'profilePictures' => $profilePictures], 200, [], ['groups' => ['profilePicture:read']]);
    }
}
