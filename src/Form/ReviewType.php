<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom et prénom'])
            ->add('profession', TextType::class, ['label' => 'Profession'])
            ->add('comment', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'class' => 'md-textarea form-control',
                    'rows' => 5
                ],
                'constraints' => [new NotBlank()],
            ])
            ->add('rating', IntegerType::class, ['label' => 'Note']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
