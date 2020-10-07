<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Imię',
                'attr' => [
                    'placeholder' => 'Jan',
                ],
            ])
            ->add('surname', null, [
                'label' => 'Nazwisko',
                'attr' => [
                    'placeholder' => 'Nowak',
                ],
            ])
            ->add('pseudonym', null, [
                'label' => 'pseudonim',
                'attr' => [
                    'placeholder' => 'Nowy',
                ],
            ])
            ->add('born', null, [
                'label' => 'Urodzony',
                'attr' => [
                    'placeholder' => '1 stycznia 1900',
                ],
            ])
            ->add('died', null, [
                'label' => 'Zmarł',
                'attr' => [
                    'placeholder' => '1 stycznia 2000',
                ],
            ])
            ->add('country', null, [
                'label' => 'Kraj pochodzenia',
                'attr' => [
                    'placeholder' => 'Wyspa marzeń',
                ],
            ])
            ->add('description', null, [
                'label' => 'Opis',
                'attr' => [
                    'rows' => 8,
                    'placeholder' => 'Historia, opis twórczości...',
                ],
            ])
            ->add('genres', null, [
                'label' => 'Gatunki',
                'multiple' => true,
                'attr' => [
                    'multiple' => true,
                ],
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('t')->where('t.deletedAt IS NULL');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
