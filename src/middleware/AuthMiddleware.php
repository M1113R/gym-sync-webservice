<?php

namespace Src\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware {
    public function __invoke(Request $request, Response $response, callable $next) {
        try {
            $token = $this->getTokenFromHeader($request);
            if (!$token) {
                throw new Exception('No token provided');
            }

            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            
            $request = $request->withAttribute('user', $decoded);
            
            return $next($request, $response);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => 'Unauthorized: ' . $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }

    private function getTokenFromHeader(Request $request): ?string {
        $header = $request->getHeaderLine('Authorization');
        
        if (empty($header)) {
            return null;
        }

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
} 