<?php
declare(strict_types=1);

namespace App;

use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{
    public function bootstrap(): void
    {
        parent::bootstrap();

        // Only try to load plugins if they're not already loaded
        if (!$this->getPlugins()->has('Authentication')) {
            $this->addPlugin('Authentication');
        }
        
        if (!$this->getPlugins()->has('Crud')) {
            $this->addPlugin('Crud');
        }
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))
            ->add(new AssetMiddleware())
            ->add(new RoutingMiddleware($this))
            ->add(new AuthenticationMiddleware($this));

        return $middlewareQueue;
    }

    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();
        
        $service->setConfig([
            'unauthenticatedRedirect' => '/users/login',
            'queryParam' => 'redirect',
        ]);

        $service->loadAuthenticator('Authentication.Jwt', [
            'secretKey' => 'your-secret-key-here',
            'algorithm' => 'HS256',
            'returnPayload' => false
        ]);
        
        $service->loadIdentifier('Authentication.Password', [
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ],
            'resolver' => [
                'className' => 'Authentication.Orm',
                'userModel' => 'Users',
            ]
        ]);

        return $service;
    }

    public function services(ContainerInterface $container): void
    {
        // Configure service container if needed
    }
}