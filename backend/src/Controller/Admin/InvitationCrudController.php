<?php

namespace App\Controller\Admin;

use App\Entity\Invitation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InvitationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invitation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('room')->setLabel('Room'),
            TextField::new('sender')->setLabel('Sender'),
            TextField::new('receiver')->setLabel('Receiver'),
            TextField::new('sentAt')->setLabel('Sent At'),
            TextField::new('acceptedAt')->setLabel('Accepted At')->hideOnForm(),
            TextField::new('revokedAt')->setLabel('Revoked At')->hideOnForm(),
            TextField::new('deniedAt')->setLabel('Denied At')->hideOnForm(),
        ];
    }
}
