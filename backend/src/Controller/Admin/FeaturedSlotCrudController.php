<?php

namespace App\Controller\Admin;

use App\Entity\FeaturedSlot;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FeaturedSlotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FeaturedSlot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Mise en avant')
            ->setEntityLabelInPlural('Mises en avant')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('slotKey', 'Clé (key)')
            ->setHelp('Ex: portrait_du_jour, a_la_une, breaking_news')
            ->setFormTypeOption('required', true);

        yield AssociationField::new('article', 'Article')
            ->setFormTypeOption('required', false)
            ->autocomplete(); // optionnel mais souvent mieux


    }
}
