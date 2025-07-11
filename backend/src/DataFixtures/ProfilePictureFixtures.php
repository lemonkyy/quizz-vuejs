<?php

namespace App\DataFixtures;

use App\Entity\ProfilePicture;

class ProfilePictureFixtures extends AbstractFixtures
{
    public function getEntityClass(): string
    {
        return ProfilePicture::class;
    }

    public function getData(): iterable
    {
        yield ['fileName' => 'profile_picture1.png'];
        yield ['fileName' => 'profile_picture2.png'];
        yield ['fileName' => 'profile_picture3.png'];
    }
}
