<?php
namespace App\Controller;

use App\Controller\AppController;

class PlansController extends AppController
{
    public function index()
    {
        // $this->Authorization->skipAuthorization(); // If using Authorization
        $plans = $this->Plans->find('all')->toArray();
        
        $this->set([
            'plans' => $plans,
            '_serialize' => ['plans'] // Only if this is an API endpoint
        ]);
        
        // For regular web views, you might want to just set the variable
        $this->set(compact('plans'));
    }
}