<?php

namespace Flashcard\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class DomainForm extends Form
{
    public function __construct($name = null, $weight_options = array(1 => 1), $weight_value = null)
    {
        parent::__construct($name);

        $this->setInputFilter($this->createInputFilter());

        $this->setAttributes(array(
            'class' => 'custom',
        ));

        // CSRF
        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);

        // Id.
        $id = new Element\Hidden('id');
        $this->add($id);

        // Name.
        $name = new Element\Text('name');
        $name->setLabel('Name');
        $this->add($name);

        // Weight.
        $weight = new Element\Select('weight');
        $weight->setLabel('Weight');
        $weight->setValueOptions($weight_options);
        $weight->setValue($weight_value);
        $this->add($weight);

        // Submit.
        $submit = new Element\Submit('submit');
        $submit->setValue('Add');
        $submit->setAttributes(array('class' => 'button secondary'));
        $this->add($submit);

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

        // Id.
        $id = new InputFilter\Input('id');
        $id->setRequired(true);
        $id->getValidatorChain()->addByName('Digits');
        $inputFilter->add($id);

        // Name.
        $name = new InputFilter\Input('name');
        $name->setRequired(true);
        $name->getFilterChain()->attachByName('StripTags');
        $name->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($name);

        // Weight.
        $weight = new InputFilter\Input('weight');
        $weight->setRequired(true);
        $weight->getValidatorChain()->addByName('Digits');
        $inputFilter->add($weight);

        return $inputFilter;
    }
}
