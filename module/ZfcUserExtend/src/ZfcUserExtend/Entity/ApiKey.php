<?php

namespace ZfcUserExtend\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Math\Rand as Rand;

/**
 * @ORM\Entity
 * @ORM\Table(name="apiKey")
 */
class ApiKey
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $keyValue;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="apiKey")
     */
    protected $user;

    public function getId()
    {
        return $this->id;
    }

    public function setKeyValue()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
        $string = Rand::getString(12, $chars, true);
        $this->keyValue = $string;
    }

    public function getKeyValue()
    {
        return $this->keyValue;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

}
