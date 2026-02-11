<?php

namespace App\Controller\Admin;

use App\Entity\Destination;
use App\Form\MediaType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;


class DestinationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Destination::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('title', 'Titre'),
            TextField::new('slug', 'Slug')->setHelp('Ex: dakar-weekend-2026'),

            TextField::new('city', 'Ville')->setRequired(false),
            TextField::new('country', 'Pays')->setRequired(false),

            TextEditorField::new('excerpt', 'Extrait')->setRequired(false),
            TextEditorField::new('content', 'Contenu')->setRequired(false),

            UrlField::new('link', 'Lien externe')
                ->setRequired(false)
                ->setHelp('Optionnel: ex https:/moroccoall.com'),


            // ✅ Plusieurs images (Media)
            CollectionField::new('media', 'Photos / Médias')
                ->setEntryType(MediaType::class)
                ->allowAdd()
                ->allowDelete()
                ->renderExpanded()
                ->setFormTypeOptions([
                    'by_reference' => false, // IMPORTANT pour addMedium/removeMedium
                ]),

            BooleanField::new('isWeekly', 'Destination de la semaine'),
            IntegerField::new('weeklyRank', 'Priorité (1 = top)')->setRequired(false),

            DateTimeField::new('publishedAt', 'Publié le')->setRequired(false),

            ChoiceField::new('status', 'Statut')->setChoices([
                'Brouillon' => Destination::STATUS_DRAFT,
                'Publié' => Destination::STATUS_PUBLISHED,
            ]),
        ];
    }
}
