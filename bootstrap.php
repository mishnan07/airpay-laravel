<?php

// Simple autoloader and helper functions
spl_autoload_register(function ($class) {
    // Handle App namespace
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
    
    // Handle Illuminate namespace
    $prefix = 'Illuminate\\';
    $base_dir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/.env');

// Helper functions
function env($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

function view($template, $data = []) {
    extract($data);
    $templatePath = __DIR__ . '/resources/views/' . str_replace('.', '/', $template) . '.php';
    
    if (!file_exists($templatePath)) {
        throw new Exception("View not found: $template");
    }
    
    ob_start();
    include $templatePath;
    return ob_get_clean();
}

function route($name) {
    $routes = [
        'airpay.form' => '/airpay-laravel/public/',
        'airpay.process' => '/airpay-laravel/public/process-payment',
        'airpay.response' => '/airpay-laravel/public/payment-response'
    ];
    
    return $routes[$name] ?? '#';
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

// Simple HTTP client
class Http {
    public static function asForm() {
        return new self();
    }
    
    public function post($url, $data) {
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        return new class($result) {
            private $response;
            
            public function __construct($response) {
                $this->response = $response;
            }
            
            public function json() {
                return json_decode($this->response, true);
            }
        };
    }
}