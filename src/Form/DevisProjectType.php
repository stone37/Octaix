<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('web', ChoiceType::class, [
                'choices' => [
                    'Site vitrine' => 'Site vitrine',
                    'Site institutionnel ou évènementiel' => 'Site institutionnel et évènementiel',
                    'Site de vente en ligne (e-commerce)' => 'Site de vente en ligne (e-commerce)',
                    'Refonte de site' => 'Refonte de site',
                    'Migration de site' => 'Migration de site',
                    'Maintenance et mise à jour de site' => 'Maintenance et mise à jour de site',
                    'Audit de site' => 'Audit de site',
                    'Application mobile' => 'application mobile',
                    'Nom de domaine et hébergement' => 'Nom de domaine et hébergement',
                    'Adresse mail pro' => 'Adresse mail pro',
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Web et mobile',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('digital', ChoiceType::class, [
                'choices' => [
                    'Campagne Facebook Ads' => 'Campagne Facebook Ads',
                    'Campagne Instagram Ads' => 'Campagne Instagram Ads',
                    'Campagne Google Ads' => 'Campagne Google Ads',
                    'Page pro sur réseaux sociaux' => 'Page pro sur réseaux sociaux',
                    'Community management' => 'Community management',
                    'Chatbot messenger' => 'Chatbot messenger',
                    'Référencement SEO de site' => 'Référencement SEO de site',
                    'Campagne emailing' => 'Campagne emailing',
                    'Rédaction d’article optimisé SEO' => 'Rédaction d’article optimisé SEO',
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Marketing',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('print', ChoiceType::class, [
                'choices' => [
                    'Logo' => 'Logo',
                    'Charte graphique' => 'Charte graphique',
                    'Branding' => 'Branding',
                    'Carte de visite' => 'Carte de visite',
                    'Flyer' => 'Flyer',
                    'Affiches publicitaires' => 'Affiches publicitaires',
                    'Brochures' => 'Brochures',
                    'Catalogues' => 'Catalogues',
                    'Papier à entête' => 'Papier à entête',
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Graphisme et print',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
