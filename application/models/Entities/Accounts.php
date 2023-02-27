<?php

use Doctrine\ORM\Mapping as ORM;

class Accounts
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->fullname;
    }

    public function setName($name)
    {
        $this->fullname = $name;
    }
}