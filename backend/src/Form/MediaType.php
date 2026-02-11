<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Image' => 'image',
                    'Vidéo' => 'video',
                    'Embed' => 'embed',
                ],
                'required' => true,
            ])

            ->add('file', VichFileType::class, [
                'label' => 'Fichier (upload)',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])

            ->add('url', TextType::class, [
                'label' => 'URL (optionnel)',
                'required' => false,
                'help' => 'Tu peux mettre une URL externe si tu ne veux pas uploader.',
            ])

            ->add('caption', TextType::class, [
                'label' => 'Légende',
                'required' => false,
            ])

            ->add('isMain', CheckboxType::class, [
                'label' => 'Photo principale',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}

