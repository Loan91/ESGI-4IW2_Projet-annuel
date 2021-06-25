<?php

namespace App\Form\Back;

use App\Entity\ProfilePicture;
use App\Entity\User;
use App\Validator\Phone;
use App\Validator\ValidPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ManageUserType extends AbstractType
{
    /** @var UserPasswordEncoderInterface $passwordEncoder L'encodeur de mot-de-passe */
    private $passwordEncoder = null;

    /** @var bool $needToEncodePassword Exprime si durant le déroulement du formulaire le mot-de-passe doit être encodé */
    private $needToEncodePassword = false;

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
            ->add('password', RepeatedType::class, [
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
                    // new ValidPassword(['minSize' => 8, 'maxSize' => 20])
                ]
            ])
            ->add('enabled', ChoiceType::class, [
                'label' => 'Activer ce compte?',
                'choices' => [
                    'Désactivé' => false,
                    'Activé' => true
                ],
                'empty_data' => 'Activé'
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
            ->add(
                $builder->create('profilePicture', FormType::class, [
                    'data_class' => ProfilePicture::class,
                    'by_reference' => true
                ])
                    ->add('imageFile', VichImageType::class, [
                        'label' => 'Photo de profil',
                        'required' => false
                    ])
            )
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Membre' => 'ROLE_USER'
                ],
            ]);

        // Transforme la données rôle d'un array vers un string pour le ChoiceType
        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            function ($roleAsArray) {
                // Quand on créer un utilisateur
                if (is_null($roleAsArray)) {
                    return null;
                }
                // Quand on met à jour un utilisateur
                return $roleAsArray[array_key_first($roleAsArray)];
            },
            function ($roleAsString) {
                return [$roleAsString];
            }
        ));

        // On edit, the password is not required
        if ($this->isMethod('PATCH', $options)) {
            $builder->get('password')->setRequired(false);
        }

        // Les données n'ont pas encore étés insérés dans l'objet user et les contraintes ne sont pas encore vérifiés.
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            if ($this->isMethod('PATCH', $options)) {
                $userData = $event->getData();
                $user = $event->getForm()->getNormData();

                if (!$user) {
                    return;
                }
                // Gère si le mot-de-passe doit être modifié ou non selon si le champ est vide
                if (empty($userData['password']['first'])) {
                    // Le champ du mot-de-passe est vide
                    $this->needToEncodePassword = false;
                    unset($userData['password']);
                    $event->setData($userData);
                } else {
                    // Le mot-de-passe a changé
                    $this->needToEncodePassword = true;
                }

                // Désactive l'utilisateur si enabled est décoché
                if (!isset($userData['enabled'])) {
                    $userData['enabled'] = false;
                    $event->setData($userData);
                }
            }
        })
            // Toutes les contraintes sont passées et les données insérés dans l'objet user.
            // Nous pouvons encoder le mot de passe s'il est ajouté ou modifié
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($options) {
                if ($this->isMethod('PATCH', $options) && $this->needToEncodePassword) {
                    $user = $event->getData();

                    // Comparaison de l'ancien et du nouveau mot de passe pour savoir s'il faut le changer
                    $oldPassword = $options['data']->getPassword();
                    $newPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
                    if ($oldPassword !== $newPassword) {
                        $event->setData($user->setPassword($newPassword));
                    }
                }

                if ($this->isMethod('POST', $options)) {
                    $newPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
                    $event->setData($user->setPassword($newPassword));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function isMethod(string $method, array $options)
    {
        return strtoupper($options['method']) === strtoupper($method);
    }
}
