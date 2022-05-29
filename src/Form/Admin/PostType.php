<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu',
                'attr'  => [
                    'class' => 'form-control md-textarea',
                    'rows'  => 5,
                ],
                'config' => array(
                    'height' => '400',
                    'toolbar' => 'full'
                ),
            ])
            ->add('online', CheckboxType::class, [
                'label' => 'Publier maintenant',
            ])
            ->add('comment', CheckboxType::class, [
                'label' => 'Autoriser les commentaires',
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $er) {
                    return $er->getEnabled();
                },
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-stone',
                ],
                'placeholder' => 'Catégorie',
                'required' => false,
            ])
            ->add('file', VichImageType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
