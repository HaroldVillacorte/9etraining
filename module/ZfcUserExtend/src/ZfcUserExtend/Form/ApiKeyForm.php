<?php

namespace ZfcUserExtend\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class ApiKeyForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setInputFilter($this->createInputFilter());

        $this->setAttributes(array(
            'class' => 'custom',
        ));

        // CSRF
        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);

        // Submit.
        $submit = new Element\Submit('submit');
        $submit->setValue('Set Key');
        $submit->setAttributes(array('class' => 'button large'));
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

        return $inputFilter;
    }
}
