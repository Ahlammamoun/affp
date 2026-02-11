<?php

namespace App\Controller\Admin;

use App\Entity\FooterLink;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class FooterLinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FooterLink::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Lien footer')
            ->setEntityLabelInPlural('Liens footer')
            ->setDefaultSort([
                'groupName' => 'ASC',
                'position' => 'ASC',
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('label', 'Label'),
            TextField::new('url', 'URL'),
            IntegerField::new('position', 'Position'),

            // ✅ IMPORTANT: évite les typos (test1, Social, legal , etc.)
            ChoiceField::new('groupName', 'Groupe')->setChoices([
                'Suivez-nous (social)' => 'social',
                'Liens légaux (legal)' => 'legal',
            ]),

            // ✅ évite NULL en base
            BooleanField::new('isActive', 'Actif')
                ->renderAsSwitch()
                ->setFormTypeOption('empty_data', true),
        ];
    }
}

