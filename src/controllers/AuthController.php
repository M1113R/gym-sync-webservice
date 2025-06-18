<?php

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Src\Config\Supabase;
use Src\Validators\AuthValidator;
use Src\Helpers\ResponseHelper;
use GuzzleHttp\Exception\ClientException;

class AuthController {
    private $supabase;

    public function __construct() {
        $this->supabase = Supabase::getInstance();
    }

    public function login(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        
        // Validate input
        $errors = AuthValidator::validateLoginData($data);
        if (!empty($errors)) {
            return ResponseHelper::error($response, $errors[0]);
        }

        try {
            // Authenticate with Supabase
            $client = $this->supabase->getClient();
            $result = $client->post('/auth/v1/token?grant_type=password', [
                'json' => [
                    'email' => $data['email'],
                    'password' => $data['password']
                ]
            ]);

            $userData = json_decode($result->getBody(), true);
            
            // Generate JWT token
            $token = JWT::encode([
                'sub' => $userData['user']['id'],
                'email' => $userData['user']['email'],
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24) // 24 hours
            ], $_ENV['JWT_SECRET'], 'HS256');

            return ResponseHelper::success($response, [
                'token' => $token,
                'user' => $userData['user']
            ]);
        } catch (ClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            $errorMessage = isset($errorResponse['error_description']) 
                ? $errorResponse['error_description'] 
                : 'Invalid credentials';
            
            return ResponseHelper::error($response, $errorMessage, $errorResponse, 401);
        } catch (\Exception $e) {
            return ResponseHelper::error($response, 'Login failed: ' . $e->getMessage(), null, 500);
        }
    }

    public function register(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        
        // Validate input
        $errors = AuthValidator::validateRegisterData($data);
        if (!empty($errors)) {
            return ResponseHelper::error($response, $errors[0]);
        }

        try {
            // Register with Supabase
            $client = $this->supabase->getClient();
            $result = $client->post('/auth/v1/signup', [
                'json' => [
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'data' => [
                        'email_confirm' => true
                    ]
                ]
            ]);

            $userData = json_decode($result->getBody(), true);
            
            return ResponseHelper::success($response, $userData, 'User registered successfully');
        } catch (ClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            $errorMessage = 'Registration failed';
            
            if (isset($errorResponse['error_code'])) {
                switch ($errorResponse['error_code']) {
                    case 'email_provider_disabled':
                        $errorMessage = 'Email registration is currently disabled';
                        break;
                    default:
                        $errorMessage = $errorResponse['msg'] ?? 'Registration failed';
                }
            }
            
            return ResponseHelper::error($response, $errorMessage, $errorResponse);
        } catch (\Exception $e) {
            return ResponseHelper::error($response, 'Registration failed: ' . $e->getMessage());
        }
    }
} 