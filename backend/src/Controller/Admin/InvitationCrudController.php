<?php

namespace App\Controller\Admin;

use App\Entity\Invitation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

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
            AssociationField::new('room')->setLabel('Room'),
            AssociationField::new('sender')->setLabel('Sender'),
            AssociationField::new('receiver')->setLabel('Receiver'),
            DateTimeField::new('sentAt')->setLabel('Sent At'),
            DateTimeField::new('acceptedAt')->setLabel('Accepted At')->hideOnForm(),
            DateTimeField::new('revokedAt')->setLabel('Revoked At')->hideOnForm(),
            DateTimeField::new('deniedAt')->setLabel('Denied At')->hideOnForm(),
        ];
    }
}
