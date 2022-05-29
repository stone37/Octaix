<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activeAchieve', CheckboxType::class, [
                'label' => 'Activer les realisations',
                'required' => false
            ])
            ->add('activeEservice', CheckboxType::class, [
                'label' => 'Activer e-services',
                'required' => false
            ])
            ->add('activeOffre', CheckboxType::class, [
                'label' => 'Activer les offres',
                'required' => false
            ])
            ->add('activePost', CheckboxType::class, [
                'label' => 'Activer le blog',
                'required' => false
            ])
            ->add('activeReview', CheckboxType::class, [
                'label' => 'Activer les témoignages',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
