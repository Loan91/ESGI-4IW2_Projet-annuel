<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Maison' => 'maison',
                    'Appartement' => 'appartement'
                ],
                'label' => 'Type de bien'
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Studio' => 'studio'
                ] + (function () {
                    $data = [];
                    for ($i = 0; $i < 15; $i++) {
                        $data['f' . $i] = 'f' . $i;
                    }
                    return $data;
                })()
            ])
            ->add('area', null, ['label' => 'Aire en m²'])
            ->add('description', null, ['help' => 'Décrivez votre bien'])
            ->add('constructionDate', null, ['label' => 'Date de construction du bien'])
            ->add('floor', null, [
                'label' => 'A quel étage se trouve le bien?',
            ])
            ->add('floors', null, [
                'label' => 'Nombre d\'étages',
                'data' => '1'
            ])
            ->add('rooms', null, [
                'label' => 'Nombre de pièces',
                'data' => '1'
            ])
            ->add('bedrooms', null, [
                'label' => 'Nombre de chambres',
                'data' => '1'
            ])
            ->add('bathrooms', null, [
                'label' => 'Nombre de salles de bain/salles d\'eau',
                'data' => '1'
            ])
            ->add('toilets', null, [
                'label' => 'Nombre de toilettes',
                'data' => '1'
            ])
            ->add('isFurnished', null, [
                'label' => 'Le bien possède t-il des meubles?'
            ])
            ->add('containsStorage', null, [
                'label' => 'Est-ce que le bien possède des éléments de stockage'
            ])
            ->add('isKitchenSeparated', null, [
                'label' => 'Est-ce que la cuisine est séparée de la salle à manger ?'
            ])
            ->add('containDiningRoom', null, [
                'label' => 'Le bien possède t-il une salle à manger?'
            ])
            ->add('ground', null, [
                'label' => 'Quel est le type de sol? (bois, inox...)'
            ])
            ->add('heater', ChoiceType::class, [
                'label' => 'Comment est installé le chauffage?',
                'choices' => [
                    'Individuel' => 'individuel',
                    'Collectif' => 'collectif'
                ]
            ])
            ->add('fireplace', null, [
                'label' => 'Y a-t-il une cheminée?'
            ])
            ->add('elevator', null, [
                'label' => 'y a-t-il un assenseur?'
            ])
            ->add('externalStorage', null, [
                'label' => 'Y a-t-il une pièce de stockage externe?'
            ])
            ->add('areaExternalStorage', null, [
                'label' => 'Quelle est la taille de cet espace?'
            ])
            ->add('guarding', null, [
                'label' => 'Y a-t-il un système de sécurité?'
            ])
            ->add('energyConsumption', null, [
                'label' => 'Combien d\'énergie consomme le bien ?',
                'data' => '1'
            ])
            ->add('gasEmissions', null, [
                'label' => 'Combien de gaz à émission ce bien émet?'
            ])
            ->add('address', null, [
                'label' => 'Adresse du bien'
            ])
            ->add('zipCode', null, [
                'label' => 'Code postal',
                'help' => 'The ZIP/Postal code for your credit card\'s billing address.',
            ])
            ->add('city', null, [
                'label' => 'Ville'
            ])
            ->add('country', null, [
                'label' => 'Pays'
            ])
            ->add('rentOrSale', ChoiceType::class, [
                'label' => 'Vente ou location?',
                'choices' => [
                    'Vente' => 'vente',
                    'Location' => 'location'
                ]
            ])
            ->add('price', null, [
                'label' => 'Prix du bien'
            ])
            ->add('charges', null, [
                'label' => 'Prix des charges du bien'
            ])
            ->add('guarentee', null, [
                'label' => 'Prix de la garantie du loyer'
            ])
            ->add('feesPrice', null, [
                'label' => 'Prix des honoraires'
            ])
            ->add('inventoryPrice', null, [
                'label' => 'Prix de la visite'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
