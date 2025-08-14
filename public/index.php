<?php

try {
    // Simple bootstrap for the Laravel-style Airpay integration
    require_once __DIR__ . '/../bootstrap.php';
    
    // Simple routing
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Remove query string and base path
    $path = parse_url($requestUri, PHP_URL_PATH);
    $path = str_replace('/airpay-laravel/public', '', $path);
    $path = str_replace('/index.php', '', $path);
    if (empty($path)) $path = '/';
    
    $airpayService = new App\Services\AirpayService();
    $controller = new App\Http\Controllers\AirpayController($airpayService);
    
    switch ($path) {
        case '/':
            if ($requestMethod === 'GET') {
                echo $controller->showPaymentForm();
            }
            break;
            
        case '/process-payment':
            if ($requestMethod === 'POST') {
                $request = new Illuminate\Http\Request($_POST);
                echo $controller->processPayment($request);
            }
            break;
            
        case '/payment-response':
            if ($requestMethod === 'POST') {
                $request = new Illuminate\Http\Request($_POST);
                echo $controller->handleResponse($request);
            }
            break;
            
        default:
            http_response_code(404);
            echo "<p>Page not found for path: $path</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}