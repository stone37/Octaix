<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('email', TextType::class, [
                'label' => 'Email'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('fax', TextType::class, [
                'label' => 'Fax',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'  => [
                    'class' => 'form-control md-textarea',
                    'rows'  => 5,
                ],
                'required' => false
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false
            ])
            ->add('district', TextType::class, [
                'label' => 'Quartier',
                'required' => false
            ])
            ->add('facebookAddress', TextType::class, [
                'label' => 'Adresse facebook',
                'required' => false
            ])
            ->add('twitterAddress', TextType::class, [
                'label' => 'Adresse twitter',
                'required' => false
            ])
            ->add('instagramAddress', TextType::class, [
                'label' => 'Adresse instagram',
                'required' => false
            ])
            ->add('youtubeAddress', TextType::class, [
                'label' => 'Adresse youtube',
                'required' => false
            ])
            ->add('linkedinAddress', TextType::class, [
                'label' => 'Adresse linkedin',
                'required' => false
            ])
            ->add('file', VichFileType::class, [
                'label' => 'Logo',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
