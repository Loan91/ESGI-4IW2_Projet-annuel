<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => "Les mot de passe doivent être identiques.",
                'required'        => true,
                'first_options'   => [
                    'label'       => 'Mot de passe',
                    'label_attr'  => [
                        'title'   => 'Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 8 caractères au moins'
                    ],
                    'attr'          => [
                        'pattern'   => "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$",
                        'title'     => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 8 caractères au moins, au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial",
                        'maxlength' => 255
                    ]
                ],
                'second_options'  => [
                    'label'       => 'Confirmez le mot de passe',
                    'label_attr'  => [
                        'title'   => 'Confirmez votre mot de passe.'
                    ],
                    'attr'          => [
                        'pattern'   => "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$",
                        'title'     => "Confirmez votre mot de passe",
                        'maxlength' => 255
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}