<?php

namespace App\Controller\Admin;

use App\Entity\LiveUpdate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class LiveUpdateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LiveUpdate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Actu en continu')
            ->setEntityLabelInPlural('Actu en continu')
            ->setDefaultSort(['happenedAt' => 'DESC'])
            ->showEntityActionsInlined()
            ->setSearchFields(['title', 'tag']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('isActive'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield BooleanField::new('isActive', 'Actif');

        yield DateTimeField::new('happenedAt', 'Heure')
            ->setHelp("Date/heure de l'info (affiché en H:i dans le ticker).");

        yield TextField::new('tag', 'Tag')
            ->setHelp('Ex: Live, Alerte (optionnel)')
            ->setRequired(false);

        yield TextField::new('title', 'Titre')
            ->setHelp('Texte court affiché dans le ticker')
            ->setRequired(true);

        // Lien interne (optionnel)
        yield AssociationField::new('article', 'Article lié')
            ->setRequired(false)
            ->setHelp('Optionnel : si renseigné, le ticker pointe vers l’article.');

        // Lien externe (optionnel)
        yield TextField::new('url', 'Lien externe')
            ->setRequired(false)
            ->setHelp('Optionnel : si pas d’article, le ticker utilise cette URL.');

        // createdAt en lecture seule (optionnel)
        yield DateTimeField::new('createdAt', 'Créé le')->onlyOnDetail();
    }
}
