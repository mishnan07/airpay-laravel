<?php
// Debug script to compare data with working PHP project

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
    'orderid' => 'TEST001',
    'currency' => '356',
    'isocurrency' => 'INR'
];

// Config
$mercid = "335854";
$username = "CKFzeZGut2";
$password = "WRx4M373";
$secret = "V8GqK8T6RC4ajHM8";

// Prepare data exactly like working PHP
$paymentData = [
    'buyer_email' => trim($data['buyerEmail']),
    'buyer_phone' => trim($data['buyerPhone']),
    'buyer_firstname' => trim($data['buyerFirstName']),
    'buyer_lastname' => trim($data['buyerLastName']),
    'buyer_address' => trim($data['buyerAddress']),
    'buyer_city' => trim($data['buyerCity']),
    'buyer_state' => trim($data['buyerState']),
    'buyer_country' => trim($data['buyerCountry']),
    'buyer_pincode' => trim($data['buyerPinCode']),
    'amount' => trim($data['amount']),
    'orderid' => trim($data['orderid']),
    'currency' => trim($data['currency']),
    'currency_code' => trim($data['currency']),
    'iso_currency' => trim($data['isocurrency']),
    'merchant_id' => $mercid
];

// Encryption exactly like working PHP
function aes256encrypt($data, $cipher, $username, $password) {
    $key = md5($username . "~:~" . $password);
    $iv = bin2hex(openssl_random_pseudo_bytes(8));
    $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $encryptedData = base64_encode($encrypted);
    return $iv . $encryptedData;
}

// Checksum exactly like working PHP
function checksumcal($postData) {
    ksort($postData);
    $data = '';
    foreach ($postData as $key => $value) {
        $data .= $value;
    }
    return hash('SHA256', $data . date('Y-m-d'));
}

// Private key exactly like working PHP
function encrypt_sha($data, $salt) {
    $key = hash('SHA256', $salt.'@'.$data);
    return $key;
}

$dataJson = json_encode($paymentData);
$request_data = aes256encrypt($dataJson, 'aes-256-cbc', $username, $password);
$checksumReq = checksumcal($paymentData);
$privatekey = encrypt_sha($username.":|:".$password, $secret);

echo "<h2>Debug Data Comparison</h2>";
echo "<h3>Payment Data JSON:</h3>";
echo "<pre>" . $dataJson . "</pre>";

echo "<h3>Encrypted Data:</h3>";
echo "<pre>" . substr($request_data, 0, 100) . "...</pre>";

echo "<h3>Checksum:</h3>";
echo "<pre>" . $checksumReq . "</pre>";

echo "<h3>Private Key:</h3>";
echo "<pre>" . $privatekey . "</pre>";

echo "<h3>Form Data to Send:</h3>";
echo "<pre>";
echo "privatekey: " . $privatekey . "\n";
echo "merchant_id: " . $mercid . "\n";
echo "encdata: " . substr($request_data, 0, 50) . "...\n";
echo "checksum: " . $checksumReq . "\n";
echo "</pre>";

// Test form
echo '<h3>Test Form (without token):</h3>';
echo '<form action="https://payments.airpay.co.in/pay/v4/index.php" method="POST">';
echo '<input type="hidden" name="privatekey" value="' . $privatekey . '">';
echo '<input type="hidden" name="merchant_id" value="' . $mercid . '">';
echo '<input type="hidden" name="encdata" value="' . $request_data . '">';
echo '<input type="hidden" name="checksum" value="' . $checksumReq . '">';
echo '<input type="hidden" name="chmod" value="">';
echo '<button type="submit">Test Payment (No Token)</button>';
echo '</form>';
?>