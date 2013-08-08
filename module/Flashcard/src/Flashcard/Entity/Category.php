<?php

namespace Flashcard\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site Variables.
 *
 * @ORM\Entity
 * @ORM\Table(name="category")
 * @property string $name
 * @property string $questions
 * @property int $id
 */
class Category
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Question", mappedBy="category")
     **/
    protected $questions;

    /**
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="categories")
     */
    protected $domain;

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    // SETTERS

    public function setName($name = '')
    {
        $this->name = (string) $name;
    }

    // UNSETTERS.

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
