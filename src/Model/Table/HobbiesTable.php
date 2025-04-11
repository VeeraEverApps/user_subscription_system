<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class HobbiesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        
        $this->setTable('hobbies');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Users');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('name', 'Hobby name is required')
            ->maxLength('name', 100, 'Hobby must be less than 100 characters');
            
        return $validator;
    }
}