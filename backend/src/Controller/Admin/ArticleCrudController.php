<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\MediaType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;



class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('title', 'Titre'),
            TextField::new('slug', 'Slug'),

            // ✅ Ajout de la rubrique (relation ManyToOne Article -> Section)
            AssociationField::new('section', 'Rubrique')
                ->setRequired(false), // mets true si ton JoinColumn est nullable: false

            TextEditorField::new('excerpt', 'Extrait'),
            TextEditorField::new('content', 'Contenu'),

            CollectionField::new('media', 'Médias')
                ->setEntryType(MediaType::class)
                ->allowAdd()
                ->allowDelete()
                ->renderExpanded()
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),

            BooleanField::new('isMustRead', 'À ne pas manquer')
                ->setHelp('Coche pour mettre cet article dans le bloc "À NE PAS MANQUER".'),

            IntegerField::new('mustReadRank', 'Priorité (optionnel)')
                ->setHelp('Plus petit = plus prioritaire. Laisse vide si pas besoin.')
                ->setRequired(false),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Brouillon' => 'draft',
                    'Publié' => 'published',
                    'Archivé' => 'archived',
                ])
                ->setRequired(true),


        ];
    }
}
