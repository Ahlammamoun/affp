<?php

namespace App\Controller\Admin;

use App\Entity\Dossier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class DossierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Dossier::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Dossier')
            ->setEntityLabelInPlural('Dossiers')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['title', 'slug', 'authorName', 'lead', 'content', 'conclusion']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('title', 'Titre');

        yield SlugField::new('slug')
            ->setTargetFieldName('title')
            ->hideOnIndex();

        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Brouillon' => 'draft',
                'Publié' => 'published',
            ]);

        yield DateTimeField::new('publishedAt', 'Publié le')->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mis à jour')->hideOnForm();

        // Auteur
        yield TextField::new('authorName', 'Auteur');
        yield TextareaField::new('authorBio', 'Bio auteur')->hideOnIndex();

        // ✅ PREVIEW sur l’index (image uploadée)
        yield ImageField::new('imageName', 'Image')
            ->setBasePath('/uploads/dossiers')
            ->onlyOnIndex();

        // ✅ UPLOAD (Vich) sur formulaire
        yield TextField::new('imageFile', 'Image (upload)')
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions([
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->onlyOnForms();

        // ✅ Fallback URL (si tu veux garder la possibilité URL externe)
        yield UrlField::new('thumb', 'Image (URL fallback)')
            ->setHelp('Optionnel : utilisé seulement si aucun upload.')
            ->onlyOnForms();

        // Contenu éditorial
        yield TextareaField::new('lead', 'Chapô (intro)')->hideOnIndex();
        yield TextareaField::new('content', 'Contenu')->hideOnIndex();
        yield TextareaField::new('conclusion', 'Conclusion')->hideOnIndex();

        // Articles liés (ManyToMany)
        yield AssociationField::new('articles', 'Articles liés')
            ->setFormTypeOptions(['by_reference' => false])
            ->autocomplete();
    }
}
