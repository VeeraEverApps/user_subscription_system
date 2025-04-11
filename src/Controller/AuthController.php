<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class AuthController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('JwtAuth');
        // $this->loadComponent('RequestHandler');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'register']);
    }

    public function login()
    {
        $this->request->allowMethod(['post']);
        
        $user = $this->Authentication->getIdentity();
        if ($user) {
            $token = $this->JwtAuth->generateToken($user->getOriginalData());
            $this->set([
                'success' => true,
                'token' => $token,
                'user' => $user,
                '_serialize' => ['success', 'token', 'user']
            ]);
        } else {
            $this->set([
                'success' => false,
                'message' => 'Invalid credentials',
                '_serialize' => ['success', 'message']
            ]);
        }
    }

    public function logout()
    {
        $this->Authentication->logout();
        $this->set([
            'success' => true,
            'message' => 'Logged out successfully',
            '_serialize' => ['success', 'message']
        ]);
    }

    public function check()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('json');
        
        $result = [
            'authenticated' => $this->Authentication->getResult()->isValid()
        ];
        
        return $this->response->withStringBody(json_encode($result));
    }
}