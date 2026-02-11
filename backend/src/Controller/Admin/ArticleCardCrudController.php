<?php

namespace App\Controller\Admin;

use App\Entity\ArticleCard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleCardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArticleCard::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article card')
            ->setEntityLabelInPlural('Article cards')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield BooleanField::new('isActive', 'Actif');

        yield TextField::new('title', 'Titre')->setFormTypeOption('required', true);
        yield TextField::new('slug', 'Slug')->setHelp('Unique')->setFormTypeOption('required', true);

        yield TextField::new('author', 'Auteur')->setFormTypeOption('required', false);
        yield DateTimeField::new('publishedAt', 'Date')->setFormTypeOption('required', false);

        yield TextareaField::new('excerpt', 'Contenu / Extrait')
            ->setFormTypeOption('required', false);

        // ✅ Upload Vich (form)
        yield TextField::new('imageFile', 'Image (upload)')
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions([
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->onlyOnForms();

        // ✅ Preview image (index/detail)
        yield ImageField::new('imageName', 'Image')
            ->setBasePath('/uploads/article-cards')
            ->onlyOnIndex();

        // fallback URL manuelle (optionnel)
        yield TextField::new('thumb', 'Thumb (URL fallback)')
            ->setHelp('Optionnel si upload utilisé')
            ->setFormTypeOption('required', false)
            ->onlyOnForms();

        yield UrlField::new('link', 'Lien externe')->setFormTypeOption('required', false);

        yield DateTimeField::new('createdAt', 'Créé le')->onlyOnIndex();
        yield DateTimeField::new('updatedAt', 'MAJ le')->onlyOnIndex();
    }
}
