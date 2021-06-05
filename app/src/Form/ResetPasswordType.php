<?php

namespace App\Form;

use App\Validator\ValidPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les deux mot de passe doivent correspondre.',
            'options' => ['attr' => ['class' => '']],
            'required' => true,
            'first_options'  => ['label' => 'Mot de passe', 'attr' => ['class' => 'mb-2']],
            'second_options' => ['label' => 'Répéter le mot de passe'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Le mot de passe ne peut pas être vide."
                ]),
                new Assert\Length([
                    'max' => 100,
                    'minMessage' => "Le nom de famille ne peut excéder 100 caractères"
                ]),
                new ValidPassword(['min' => 8])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
