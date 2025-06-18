<?php

namespace Src\Helpers;

use Psr\Http\Message\ResponseInterface as Response;

class ResponseHelper {
    public static function json(Response $response, $data, int $status = 200): Response {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public static function error(Response $response, string $message, $details = null, int $status = 400): Response {
        $data = [
            'error' => true,
            'message' => $message
        ];

        if ($details !== null) {
            $data['details'] = $details;
        }

        return self::json($response, $data, $status);
    }

    public static function success(Response $response, $data = null, string $message = 'Success'): Response {
        $responseData = [
            'error' => false,
            'message' => $message
        ];

        if ($data !== null) {
            $responseData['data'] = $data;
        }

        return self::json($response, $responseData);
    }
} 