<?php
use Migrations\AbstractSeed;

class PlansSeed extends AbstractSeed
{
    public function run(): void 
    {
        $data = [
            [
                'name' => 'Basic',
                'price' => 9.99,
                'description' => 'Basic subscription plan',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Standard',
                'price' => 19.99,
                'description' => 'Standard subscription plan',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Premium',
                'price' => 29.99,
                'description' => 'Premium subscription plan',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ]
        ];

        $table = $this->table('plans');
        $table->insert($data)->save();
    }
}