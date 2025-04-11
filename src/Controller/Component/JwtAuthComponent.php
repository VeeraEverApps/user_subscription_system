<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuthComponent extends Component
{
    private $key = 'your-secret-key'; // Change this to a strong secret key

    public function generateToken($user)
    {
        $payload = [
            'iss' => 'your-issuer', // Issuer
            'sub' => $user['id'], // Subject
            'iat' => time(), // Issued at
            'exp' => time() + 3600, // Expire in 1 hour
            'user' => $user
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function decodeToken($token)
    {
        try {
            return JWT::decode($token, new Key($this->key, 'HS256'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUserFromToken($token)
    {
        $decoded = $this->decodeToken($token);
        return $decoded ? (array)$decoded->user : false;
    }
}