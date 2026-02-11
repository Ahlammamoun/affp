<?php

namespace App\Controller\Admin;

use App\Entity\AdSlide;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdSlideCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdSlide::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Slide publicitaire')
            ->setEntityLabelInPlural('Zone commerciale')
            ->setDefaultSort(['position' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield BooleanField::new('isActive', 'Actif');
        yield IntegerField::new('position', 'Ordre');

        yield TextField::new('badge');
        yield TextField::new('title');
        yield TextField::new('text');
        yield TextField::new('href');
    }
}
