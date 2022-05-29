<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre nom et prénom',
                'constraints' => new NotBlank(),
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Votre numéro',
                'constraints' => new NotBlank(),
            ])
            ->add('content',  TextareaType::class, [
                'label' => 'Décrivez-nous votre projet',
                'attr' => [
                    'class' => 'md-textarea form-control',
                    'rows' => 6
                ],
                'constraints' => new NotBlank(),
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Porteur de projet' => 'Porteur de projet',
                    'Indépendant' => 'Indépendant',
                    'Dirigeant d\'une TPE/PME' => 'Dirigeant d\'une TPE/PME',
                    'Responsable marketing/Projet digital' => 'Responsable marketing/Projet digital',
                    'Association' => 'Association',
                ],
                'label' => 'Qui êtes-vous ?',
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'placeholder' => 'Qui êtes-vous ?',
                'constraints' => new NotBlank(),
            ])
            ->add('budget', ChoiceType::class, [
                'choices' => [
                    'Indéterminé' => 'Indéterminé',
                    '- de 200 000 <span>CFA</span>' => '- de 200 000',
                    '200 000 <span>CFA</span> à 300 000 <span>CFA</span>' => '200 000 CFA à 300 000 CFA',
                    '300 000 <span>CFA</span> à 500 000 <span>CFA</span>' => '300 000 CFA à 500 000 CFA',
                    '500 000 <span>CFA</span> à 800 000 <span>CFA</span>' => '500 000 CFA à 800 000 CFA',
                    '800 000 <span>CFA</span> à 1 200 000 <span>CFA</span>' => '800 000 CFA à 1 200 000 CFA',
                    '1 200 000 <span>CFA</span> à 1 500 000 <span>CFA</span>' => '1 200 000 CFA à 1 500 000 CFA',
                    '+ de 1 500 000 <span>CFA</span>' => '+ de 1 500 000 CFA',
                ],
                'label' => 'Sélectionnez votre budget (Ce champ est obligatoire)',
                'expanded' => true,
                'multiple' => false,
                'constraints' => new NotBlank(),
            ])
            ->add('project', DevisProjectType::class, [
                'constraints' => new NotBlank(),
                'label' => 'Sélectionnez vos prestations (Ce champ est obligatoire)'
            ])
            ->add('delay', IntegerType::class, [
                'label' => 'Précisez nous votre délai (Jours)',
                'constraints' => new NotBlank(),
            ])
            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'app-recaptchaToken']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
