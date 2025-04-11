<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Security;
use Cake\Http\Exception\BadRequestException;

class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // $this->loadComponent('Authentication.Authentication');
        
        // Only load Authorization if plugin exists
        if (class_exists('Authorization\AuthorizationServiceProviderInterface')) {
            $this->loadComponent('Authorization.Authorization');
        }
        
        // $this->loadComponent('RequestHandler');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // $this->Authentication->allowUnauthenticated(['login', 'register']);
        
        if (isset($this->Authorization)) {
            $this->Authorization->skipAuthorization(['login', 'register']);
        }
    }

    public function register()
    {
        $this->request->allowMethod(['get', 'post', 'ajax']);
        $user = $this->Users->newEmptyEntity();
        $redirectUrl = $this->request->getQuery('redirect') ?? '/dashboard';
        $planId = $this->request->getQuery('plan_id');

        // Handle AJAX validation
        if ($this->request->is('ajax') && $this->request->is('get')) {
            $this->autoRender = false;
            $field = $this->request->getQuery('field');
            $value = $this->request->getQuery('value');
            
            if (!$field || !$value) {
                throw new BadRequestException('Missing validation parameters');
            }

            $user = $this->Users->patchEntity($user, [$field => $value], [
                'validate' => 'register'
            ]);

            return $this->response->withType('json')
                ->withStringBody(json_encode([
                    'valid' => empty($user->getError($field)),
                    'message' => $user->getError($field)[0] ?? null
                ]));
        }

        if ($this->request->is(['post', 'ajax'])) {
            $data = $this->request->getData();
            
            // Handle file upload
            if (!empty($data['profile_picture']) && is_object($data['profile_picture'])) {
                if ($data['profile_picture']->getError() === UPLOAD_ERR_OK) {
                    $file = $data['profile_picture'];
                    $filename = Security::hash($file->getClientFilename()) . '.' . pathinfo(
                        $file->getClientFilename(),
                        PATHINFO_EXTENSION
                    );
                    $file->moveTo(WWW_ROOT . 'uploads' . DS . $filename);
                    $data['profile_picture'] = $filename;
                } else {
                    unset($data['profile_picture']);
                }
            }

            $user = $this->Users->patchEntity($user, $data, [
                'validate' => 'register',
                'associated' => ['Hobbies']
            ]);

            // AJAX response
            if ($this->request->is('ajax')) {
                $this->autoRender = false;
                $this->response = $this->response->withType('json');
                
                if ($this->Users->save($user)) {
                    $this->Authentication->setIdentity($user);
                    return $this->response->withStringBody(json_encode([
                        'success' => true,
                        'redirect' => $redirectUrl,
                        'message' => 'Registration successful!'
                    ]));
                }
                
                return $this->response->withStringBody(json_encode([
                    'success' => false,
                    'errors' => $user->getErrors(),
                    'message' => 'Please fix the errors below'
                ]));
            }

            // Regular form submission
            if ($this->Users->save($user)) {
                $this->Authentication->setIdentity($user);
                $this->Flash->success(__('Registration successful!'));
                return $this->redirect($redirectUrl);
            }
            $this->Flash->error(__('Please fix the errors below.'));
        }

        $plan = $planId ? $this->Users->Plans->findById($planId)->first() : null;
        $this->set(compact('user', 'plan', 'redirectUrl'));
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post', 'ajax']);
        $redirectUrl = $this->request->getQuery('redirect') ?? '/dashboard';
        $planId = $this->request->getQuery('plan_id');

        if ($this->request->is(['post', 'ajax'])) {
            $result = $this->Authentication->getResult();
            
            // AJAX response
            if ($this->request->is('ajax')) {
                $this->autoRender = false;
                $this->response = $this->response->withType('json');
                
                if ($result->isValid()) {
                    return $this->response->withStringBody(json_encode([
                        'success' => true,
                        'redirect' => $redirectUrl,
                        'message' => 'Login successful'
                    ]));
                }
                
                return $this->response->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ]));
            }

            // Regular form submission
            if ($result->isValid()) {
                return $this->redirect($redirectUrl);
            }
            
            if ($this->request->is('post') && !$result->isValid()) {
                $this->Flash->error(__('Invalid email or password'));
            }
        }

        $this->set(compact('redirectUrl', 'planId'));
    }

    public function logout()
    {
        $this->request->allowMethod(['post']);
        $this->Authentication->logout();
        
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            return $this->response->withType('json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'redirect' => '/login'
                ]));
        }
        
        return $this->redirect(['action' => 'login']);
    }

    public function dashboard()
    {
        $this->request->allowMethod(['get']);
        $user = $this->Authentication->getIdentity();
        $this->set(compact('user'));
    }

    public function forgotPassword()
    {
        $this->request->allowMethod(['post', 'ajax']);
        
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->response = $this->response->withType('json');
            
            // Implement your password reset logic here
            $email = $this->request->getData('email');
            
            return $this->response->withStringBody(json_encode([
                'success' => true,
                'message' => 'If that email exists, we sent a reset link'
            ]));
        }
        
        // Regular form submission handling
        $this->Flash->success(__('If that email exists, we sent a reset link'));
        return $this->redirect(['action' => 'login']);
    }
}