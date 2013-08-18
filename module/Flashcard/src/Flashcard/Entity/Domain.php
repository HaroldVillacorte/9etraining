<?php

namespace Flashcard\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site Variables.
 *
 * @ORM\Entity
 * @ORM\Table(name="domain")
 * @property string $name
 * @property string $categories
 * @property int $id
 */
class Domain
{

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
     * @ORM\OneToMany(targetEntity="Category", mappedBy="domain")
     **/
    protected $categories;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weight;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = (string) $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setWeight($weight)
    {
        $this->weight = (int) $weight;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getCategories()
    {
        return $this->categories;
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
