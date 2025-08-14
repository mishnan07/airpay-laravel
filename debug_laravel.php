<?php
// Debug Laravel service vs working test

require_once __DIR__ . '/bootstrap.php';

use App\Services\AirpayService;

// Test data
$data = [
    'buyerEmail' => 'test@test.com',
    'buyerPhone' => '1234567890',
    'buyerFirstName' => 'Test',
    'buyerLastName' => 'User',
    'buyerAddress' => 'Test Address',
    'buyerCity' => 'Mumbai',
    'buyerState' => 'Maharashtra',
    'buyerCountry' => 'India',
    'buyerPinCode' => '400001',
    'amount' => '100.00',
    'orderid' => 'TEST' . time(),
    'currency' => '356',
    'isocurrency' => 'INR'
];

echo "<h2>Laravel Service Debug</h2>";

try {
    $airpayService = new AirpayService();
    $redirectData = $airpayService->initiatePayment($data);
    
    echo "<h3>Laravel Service Output:</h3>";
    echo "<pre>" . json_encode($redirectData, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<h3>Laravel Test Form:</h3>";
    echo '<form action="' . $redirectData['url'] . '" method="POST">';
    echo '<input type="hidden" name="privatekey" value="' . $redirectData['privatekey'] . '">';
    echo '<input type="hidden" name="merchant_id" value="' . $redirectData['merchant_id'] . '">';
    echo '<input type="hidden" name="encdata" value="' . $redirectData['encdata'] . '">';
    echo '<input type="hidden" name="checksum" value="' . $redirectData['checksum'] . '">';
    echo '<input type="hidden" name="chmod" value="">';
    echo '<button type="submit">Test Laravel Service</button>';
    echo '</form>';
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>