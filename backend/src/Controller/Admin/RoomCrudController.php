<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RoomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Room::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('owner')->setLabel('Owner'),
            TextField::new('createdAt')->setLabel('Created At')->hideOnForm(),
            TextField::new('isPublic')->setLabel('Is Public'),
            TextField::new('deletedAt')->setLabel('Deleted At')->hideOnForm(),
            TextField::new('code')->setLabel('Room Code'),
        ];
    }
}
