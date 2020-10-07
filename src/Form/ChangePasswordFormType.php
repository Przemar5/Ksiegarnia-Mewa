<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Nowe hasło',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Hasło jest wymagane.',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Hasło powinno mieć co najmniej 8 znaków.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", 
                            'message' => 'Hasło powinno zawierać co najmniej 1 małą literę, 1 dużą literę, 1 cyfrę i 1 znak specjalny.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Powtrórz hasło',
                ],
                'invalid_message' => 'Oba hasła muszą być identyczne.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
