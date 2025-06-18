<?php

namespace Src\Config;

use GuzzleHttp\Client;

class Supabase {
    private static $instance = null;
    private $client;
    private $url;
    private $key;

    private function __construct() {
        $this->url = $_ENV['SUPABASE_URL'];
        $this->key = $_ENV['SUPABASE_KEY'];
        
        $this->client = new Client([
            'base_uri' => $this->url,
            'headers' => [
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json'
            ],
            'verify' => false // Disallow SSL verification during development
        ]);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getClient() {
        return $this->client;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getKey() {
        return $this->key;
    }
} 