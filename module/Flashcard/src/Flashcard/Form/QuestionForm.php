<?php

namespace Flashcard\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class QuestionForm extends Form
{
    public function __construct($name = null, $category_options = array(), $set_value = array())
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
        
        // Question.
        $question = new Element\Textarea('question');
        $question->setLabel('Question');
        $this->add($question);
        
        // Answer.
        $answer = new Element\Textarea('answer');
        $answer->setLabel('Answer');
        $this->add($answer);
        
        // Note.
        $note = new Element\Textarea('note');
        $note->setLabel('Note');
        $this->add($note);
        
        // Category.
        $category = new Element\Radio('category_options');
        $category->setLabel('Category');
        $category->setValueOptions($category_options);
        $category->setValue($set_value);
        $this->add($category);

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
        $id->getFilterChain()->attachByName('StripTags');
        $id->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($id);
        
        // Question.
        $question = new InputFilter\Input('question');
        $question->setRequired(true);
        $question->getFilterChain()->attachByName('StripTags');
        $question->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($question);
        
        // Answer.
        $answer = new InputFilter\Input('answer');
        $answer->setRequired(true);
        $answer->getFilterChain()->attachByName('StripTags');
        $answer->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($answer);
        
        // Note.
        $note = new InputFilter\Input('note');
        $note->setRequired(false);
        $note->getFilterChain()->attachByName('StripTags');
        $note->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($note);
        
        // Category.
        $category = new InputFilter\Input('category_options');
        $category->setRequired(true);
        $category->getValidatorChain()->addByName('Digits');
        $category->getFilterChain()->attachByName('StripTags');
        $category->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($category);
        
        return $inputFilter;
    }
}
