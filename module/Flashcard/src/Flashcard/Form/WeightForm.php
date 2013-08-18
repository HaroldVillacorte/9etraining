<?php

namespace Flashcard\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class WeightForm extends Form
{
    public function __construct($name = 'weight')
    {
        parent::__construct($name);

        $this->setInputFilter($this->createInputFilter());

        $this->setAttributes(array(
            'class' => 'weight-form',
        ));

        // CSRF
        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);

        // Weight.
        $weight = new Element\Hidden('weight');
        $this->add($weight);
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // Csrf
        $csrf = new InputFilter\Input('csrf');
        $csrf->setRequired(true);
        $csrf->getValidatorChain()->addByName('Csrf');
        $csrf->getFilterChain()->attachByName('StripTags');
        $csrf->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($csrf);

        // Weight.
        $weight = new InputFilter\Input('weight');
        $weight->setRequired(true);
        $weight->getValidatorChain()->addByName('Digits');
        $inputFilter->add($weight);

        return $inputFilter;
    }
}
