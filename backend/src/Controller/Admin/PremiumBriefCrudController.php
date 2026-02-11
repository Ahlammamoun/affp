<?php

namespace App\Controller\Admin;

use App\Entity\PremiumBrief;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class PremiumBriefCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PremiumBrief::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title')->setRequired(true);

        yield SlugField::new('slug')
            ->setTargetFieldName('title')
            ->setRequired(true);

        yield ChoiceField::new('scope')
            ->setChoices([
                'Afrique' => 'afrique',
                'Région' => 'region',
                'Pays' => 'pays',
                'Thème' => 'theme',
            ])
            ->setRequired(true);

        yield TextField::new('scopeLabel')
            ->setHelp('Ex: Afrique de l’Ouest, Sahel, RDC, Économie')
            ->hideOnIndex();

        // HTML (si tu as un WYSIWYG, tu peux remplacer par un champ custom)
        yield TextareaField::new('summaryHtml')
            ->setHelp('Contenu premium (HTML possible)')
            ->hideOnIndex();

        yield ArrayField::new('bullets')
            ->setHelp('Liste des points clés (JSON/array)')
            ->hideOnIndex();

        yield ArrayField::new('tags')
            ->setHelp('Tags (ex: sahel, sécurité, élections)')
            ->hideOnIndex();

        yield ChoiceField::new('status')
            ->setChoices([
                'Draft' => 'draft',
                'Publié' => 'published',
            ]);

        yield DateTimeField::new('publishedAt')
            ->setHelp('Optionnel : date de publication')
            ->hideOnIndex();

        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();

        // Optionnel: petit indicateur
        yield BooleanField::new('isPublished')
            ->onlyOnIndex()
            ->renderAsSwitch(false);
    }
}
