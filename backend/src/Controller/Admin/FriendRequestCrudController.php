<?php

namespace App\Controller\Admin;

use App\Entity\FriendRequest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FriendRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FriendRequest::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('sender')->setLabel('Sender'),
            TextField::new('receiver')->setLabel('Receiver'),
            TextField::new('sentAt')->setLabel('Sent At'),
            TextField::new('acceptedAt')->setLabel('Accepted At')->hideOnForm(),
            TextField::new('revokedAt')->setLabel('Revoked At')->hideOnForm(),
            TextField::new('deniedAt')->setLabel('Denied At')->hideOnForm(),
        ];
    }
}

