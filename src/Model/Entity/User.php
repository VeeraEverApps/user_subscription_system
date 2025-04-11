<?php

use Authentication\IdentityInterface;
use Cake\ORM\Entity;

class User extends Entity implements IdentityInterface
{
    public function getIdentifier()
    {
        return $this->id;
    }

    public function getOriginalData()
    {
        return $this;
    }
}