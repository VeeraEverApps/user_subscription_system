<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateInitialSchema extends AbstractMigration
{
    public function change()
    {
        $plans = $this->table('plans');
        $plans->addColumn('name', 'string', ['limit' => 100])
              ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2])
              ->addColumn('description', 'text')
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->create();

        $users = $this->table('users');
        $users->addColumn('plan_id', 'integer', ['null' => true])
              ->addColumn('name', 'string', ['limit' => 100])
              ->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('password', 'string')
              ->addColumn('profile_picture', 'string', ['null' => true])
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->addIndex(['email'], ['unique' => true])
              ->create();

        $hobbies = $this->table('hobbies');
        $hobbies->addColumn('user_id', 'integer')
                ->addColumn('name', 'string', ['limit' => 100])
                ->addColumn('created', 'datetime')
                ->addColumn('modified', 'datetime')
                ->create();
    }
}