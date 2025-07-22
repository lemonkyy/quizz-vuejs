<?php

namespace App\Controller\Admin;

use App\Entity\ProfilePicture;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ProfilePictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProfilePicture::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ImageField::new('fileName')
                ->setLabel('Profile Picture')
                ->setUploadDir('public/images/profile_pictures')
                ->setBasePath('images/profile_pictures')
                ->setRequired(true)
                ->setHelp('Upload a profile picture')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
        ];
    }
}
