<?php

namespace App\Form;

use App\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nazwa',
            ])
            ->add('started_at', null, [
                'label' => 'Powstał',
                'attr' => [
                    'placeholder' => 'Data',
                ],
            ])
            ->add('ended_at', null, [
                'label' => 'Zakończył się',
                'attr' => [
                    'placeholder' => 'Data',
                ],
            ])
            ->add('origin', null, [
                'label' => 'Powstał w',
            ])
            ->add('description', null, [
                'label' => 'Opis',
                'attr' => [
                    'rows' => 8,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Genre::class,
        ]);
    }
}
