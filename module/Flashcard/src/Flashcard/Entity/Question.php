<?php

namespace Flashcard\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Site variables.
*
* @ORM\Entity
* @ORM\Table(name="question")
* @property string $question
* @property string $answer
* @property string $category
* @property int $id
*/
class Question
{

    /**
    * @ORM\Id
    * @ORM\Column(type="integer");
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
    * @ORM\Column(type="text")
    */
    protected $question;

    /**
    * @ORM\Column(type="text")
    */
    protected $answer;
    
    /**
    * @ORM\Column(type="text", nullable=true)
    */
    protected $note;

    /**
    * @ORM\ManyToOne(targetEntity="Category", inversedBy="questions")
    */
    protected $category;

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getAnswer()
    {
        return $this->answer;
    }
    
    public function getNote()
    {
        return $this->note;
    }

    public function getCategory()
    {
        return $this->category;
    }

    // SETTERS

    public function setQuestion($question = '')
    {
        $this->question = (string) $question;
    }

    public function setAnswer($answer = '')
    {
        $this->answer = (string) $answer;
    }
    
    public function setNote($note = '')
    {
        $this->note = (string) $note;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    // REMOVERS

    public function removeCategory(Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
    * Convert the object to an array.
    *
    * @return array
    */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
}
