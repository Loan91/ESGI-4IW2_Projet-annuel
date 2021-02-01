<?php

namespace App\Twig;

use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isCheckbox', [$this, 'isCheckbox']),
            new TwigFunction('isSelectBox', [$this, 'isSelectBox']),
            new TwigFunction('isTextArea', [$this, 'isTextArea']),
        ];
    }

    /**
     * Analyse a FormView instance row and tells if it is a checkbox row.
     * 
     * @param FormView $row The row to check
     */
    public function isCheckbox(FormView $row)
    {
        return ($row->vars['block_prefixes'][1] === 'checkbox');
    }

    /**
     * Analyse a FormView instance row and tells if it is a select box row.
     * 
     * @param FormView $row The row to check
     */
    public function isSelectBox(FormView $row)
    {
        return ($row->vars['block_prefixes'][1] === 'choice');
    }

    /**
     * Analyse a FormView instance row and tells if it is a textarea row.
     * 
     * @param FormView $row The row to check
     */
    public function isTextArea(FormView $row)
    {
        return ($row->vars['block_prefixes'][1] === 'text'
            && $row->vars['block_prefixes'][1] === 'textarea');
    }
}
