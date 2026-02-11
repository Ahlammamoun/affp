<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            // ✅ preview image dans la liste
            ImageField::new('imageName')
                ->setLabel('Photo')
                ->setBasePath('/uploads/events')
                ->onlyOnIndex(),

            TextField::new('title'),
            TextField::new('slug')->setHelp('Ex: concert-burna-paris-2026'),

            ChoiceField::new('category')->setChoices([
                'Musique' => Event::CAT_MUSIQUE,
                'Sport' => Event::CAT_SPORT,
                'Culture' => Event::CAT_CULTURE,
                'Conférence' => Event::CAT_CONFERENCE,
                'Compétition' => Event::CAT_COMPETITION,
                'Autre' => Event::CAT_AUTRE,
            ]),

            TextField::new('city')->setRequired(false),
            TextField::new('country')->setRequired(false),

            DateTimeField::new('eventAt')->setHelp('Date & heure de l’événement'),

            UrlField::new('link')->setRequired(false)->setHelp('Billetterie / page info'),

            TextareaField::new('description')->setRequired(false),

            // ✅ upload image (form only)
            Field::new('imageFile')
                ->setLabel('Photo (upload)')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),

            ChoiceField::new('status')->setChoices([
                'Draft' => Event::STATUS_DRAFT,
                'Published' => Event::STATUS_PUBLISHED,
            ]),
        ];
    }
}
