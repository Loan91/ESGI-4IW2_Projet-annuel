<?php

namespace App\Validator;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @Annotation
 */
class ValidPassword extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $minSizeMessage = 'Le mot-de-passe doit avoir minimum {{ value }} caractères';
    public $maxSizeMessage = 'Le mot-de-passe doit avoir maximum {{ value }} caractères';
    public $containMessage = 'Le mot-de-passe doit contenir au moins 1 chiffre, une minuscule, une majuscule et un caractère spécial';

    public $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        // On s'assure que la taille la plus petite soit strictement égale ou inférieure à la plus grande
        if ($this->options['minSize'] > $this->options['maxSize']) {
            throw new InvalidArgumentException('L\'argument minSize (' . $this->options['minSize'] . ') doit être plus petit ou égal à maxSize (' . $this->options['maxSize'] . ')');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define('minSize')
            ->allowedTypes('int')
            ->default(8);
        $resolver->define('maxSize')
            ->allowedTypes('int')
            ->default(20);
    }
}
