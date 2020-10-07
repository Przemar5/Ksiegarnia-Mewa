<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'label' => 'Imię',
                'attr' => [
                    'placeholder' => 'Jan',
                ],
            ])
            ->add('surname', null, [
                'required' => true,
                'label' => 'Nazwisko',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nowak',
                ],
            ])
            ->add('country', null, [
                'required' => true,
                'label' => 'Kraj',
                'attr' => [
                    'placeholder' => 'Nibylandia',
                ],
            ])
            ->add('city', null, [
                'required' => true,
                'label' => 'Miasto',
                'attr' => [
                    'placeholder' => 'Gdziesiowo',
                ],
            ])
            ->add('address', null, [
                'required' => true,
                'label' => 'Adres do odbioru przesyłki',
                'attr' => [
                    'placeholder' => 'ul. Przykładowa 1/2',
                ],
            ])
            ->add('postalCode', null, [
                'required' => true,
                'label' => 'Kod pocztowy',
                'attr' => [
                    'placeholder' => '12-345',
                ],
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'label' => 'Telefon kontaktowy',
                'help' => 'Format "123-456-789" lub "+48 123-456-789".',
                'attr' => [
                    'placeholder' => '123-456-789',
                ],
            ])
            ->add('additionalPhone', TelType::class, [
                'required' => false,
                'label' => 'Dodatkowy telefon (opcjonalny)',
                'help' => 'Format ten sam co powyżej.',
                'attr' => [
                    'placeholder' => '123-456-789',
                ],
            ])
            ->add('paymentForm', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Forma płatności',
                'choices' => [
                    'Płatność przy odbiorze' => 'Płatność przy odbiorze',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
