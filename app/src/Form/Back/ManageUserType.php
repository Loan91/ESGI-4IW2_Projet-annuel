<?php

namespace App\Form\Back;

use App\Entity\User;
use App\Validator\Phone;
use App\Validator\ValidPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;


class ManageUserType extends AbstractType
{
    /** @var UserPasswordEncoderInterface $passwordEncoder L'encodeur de mot-de-passe */
    private $passwordEncoder = null;

    /** @var bool $passwordChanged Exprime si durant le déroulement du formulaire le mot-de-passe doit être encodé */
    private $passwordChanged = false;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => [
                    'M.' => 'Monsieur',
                    'Mme./Mlle.' => 'Madame'
                ],
                'empty_data' => '',
                'expanded' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "La civilité ne peut pas être vide."
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le prénom ne peut pas être vide."
                    ])
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le nom ne peut pas être vide."
                    ])
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'email ne peut pas être vide."
                    ]),
                    new Assert\Email([
                        'message' => "L'email {{ value }} n'est pas une adresse email valide."
                    ])
                ]
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Activer ce compte?',
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le numéro de téléphone ne peut pas être vide."
                    ]),
                    new Phone()
                ]
            ])
            // Set le champ mpt-de-passe différemmement selon que ce soit une création ou un update
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                if (strtoupper($options['method']) === 'POST') {
                    $event->getForm()->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les deux mot de passe doivent correspondre.',
                        'options' => ['attr' => ['class' => '']],
                        'required' => true,
                        'first_options'  => ['label' => 'Mot de passe', 'attr' => ['class' => 'mb-2'], 'empty_data' => ''],
                        'second_options' => ['label' => 'Répéter le mot de passe', 'empty_data' => ''],
                        'constraints' => [
                            new Assert\NotBlank([
                                'message' => "Le mot de passe ne peut pas être vide."
                            ]),
                            new Assert\Length([
                                'max' => 100,
                                'minMessage' => "Le nom de famille ne peut excéder 100 caractères"
                            ]),
                            new ValidPassword()
                        ]
                    ]);
                } elseif (strtoupper($options['method']) === 'PATCH') {
                    $event->getForm()->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les deux mot de passe doivent correspondre.',
                        'options' => ['attr' => ['class' => '']],
                        'required' => false,
                        'first_options'  => ['label' => 'Mot de passe', 'attr' => ['class' => 'mb-2'], 'empty_data' => ''],
                        'second_options' => ['label' => 'Répéter le mot de passe', 'empty_data' => ''],
                        'constraints' => [
                            new Assert\NotBlank([
                                'message' => "Le mot de passe ne peut pas être vide."
                            ]),
                            new Assert\Length([
                                'max' => 100,
                                'minMessage' => "Le nom de famille ne peut excéder 100 caractères"
                            ]),
                            new ValidPassword(['minSize' => 8, 'maxSize' => 20])
                        ]
                    ]);
                }
            })
            // Retire le mot de passe de la modification si le champ est vide OU enregistre le besoin de l'encoder après le check des constraints.
            // Le choix de PRE_SUBMIT se justifie car les données n'ont pas encore étés insérés dans l'objet user. Egalement, les contraintes ne sont pas encore vérifiés.
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                if (strtoupper($options['method']) === 'PATCH') {
                    $userData = $event->getData();
                    $user = $event->getForm()->getNormData();

                    if (!$user) {
                        return;
                    }

                    if (empty($userData['password']['first'])) {
                        // Le champ du mot-de-passe est vide
                        unset($userData['password']);
                        $event->setData($userData);
                    } else {
                        // Le mot-de-passe a changé
                        $this->passwordChanged = true;
                    }
                }
            })
            // Toutes les contraintes sont passées, si besoin est nous pouvons maintenant encoder le mot de passe
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($options) {
                if ($this->passwordChanged) {
                    $user = $event->getData();
                    // Comparaison de l'ancien et du nouveau mot de passe pour savoir s'il faut le changer
                    $oldPassword = $options['data']->getPassword();
                    $newPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
                    if ($oldPassword !== $newPassword) {
                        $event->setData($user->setPassword($newPassword));
                    }
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
