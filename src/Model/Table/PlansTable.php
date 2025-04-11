<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class PlansTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('plans');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Users');
    }
}