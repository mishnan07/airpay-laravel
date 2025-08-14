<?php
// Final test with complete working format including token

// Include working functions
class functions
{
    public static function aes256encrypt($data, $cipher , $username, $password)
    {
        $key            =   md5($username . "~:~" . $password);
        $iv             =   bin2hex(openssl_random_pseudo_bytes(8));
        $encrypted      =   openssl_encrypt($data, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $encryptedData  =   base64_encode($encrypted);
        return $iv . $encryptedData;
    }

    public static function encrypt($request, $secretKey)
    {
        $iv  =   bin2hex(openssl_random_pseudo_bytes(8));
        $raw = openssl_encrypt($request, 'aes-256-cbc', $secretKey, OPENSSL_RAW_DATA, $iv);
        $data = $iv . base64_encode($raw);
        return $data;
    }

    public static function decrypt($requestData, $secretKey)
    {
        $data = $requestData['response'];
        $iv = substr($data, 0, 16);
        $encryptedData = substr($data, 16);
        $raw = openssl_decrypt(base64_decode($encryptedData), 'AES-256-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
        return $raw;
    }

    public static function encrypt_sha($data, $salt) {
        $key = hash('SHA256', $salt.'@'.$data);
        return $key;
    }

    public static function checksumcal($postData)
    {
        ksort($postData);
        $data = '';
        foreach ($postData as $key => $value) {
            $data .= $value;
        }
        return hash('SHA256', $data . date('Y-m-d'));
    }

    public static function sendPostData($tokenUrl, $postData)
    {
        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

// Config
$mercid = "335854";
$username = "CKFzeZGut2";
$password = "WRx4M373";
$secret = "V8GqK8T6RC4ajHM8";
$client_id = "e38135";
$client_secret = "30af9b1913d535990a3c39138f4353c0";
$tokenUrl = "https://kraken.airpay.co.in/airpay/pay/v4/api/oauth2/token.php";
$URL = 'https://payments.airpay.co.in/pay/v4/index.php';

// Get token
$request = array();
$request['client_id'] = $client_id;
$request['client_secret'] = $client_secret;
$request['grant_type'] = 'client_credentials';
$request['merchant_id'] = $mercid;

$secretKey = md5($username ."~:~" .$password);
$encre = functions::encrypt(json_encode($request), $secretKey);

$req = [
    'merchant_id' => $request['merchant_id'],
    "encdata" => $encre,
    "checksum" => functions::checksumcal($request)
];

$access_token = functions::sendPostData($tokenUrl, $req);
$decryptData = functions::decrypt(json_decode($access_token, true), $secretKey);
$tokenResponse = json_decode($decryptData, true);

if (isset($tokenResponse['status']) && $tokenResponse['status'] === 'success') {
    $accessToken = $tokenResponse['data']['access_token'];
    $URL .= '?token=' . $accessToken;
    
    // Payment data
    $data = [];
    $data['buyer_email'] = 'test@test.com';
    $data['buyer_phone'] = '1234567890';
    $data['buyer_firstname'] = 'Test';
    $data['buyer_lastname'] = 'User';
    $data['buyer_address'] = 'Test Address';
    $data['buyer_city'] = 'Mumbai';
    $data['buyer_state'] = 'Maharashtra';
    $data['buyer_country'] = 'India';
    $data['buyer_pincode'] = '400001';
    $data['amount'] = '100.00';
    $data['orderid'] = 'TEST' . time(); // Unique order ID
    $data['currency'] = '356';
    $data['currency_code'] = '356';
    $data['iso_currency'] = 'INR';
    $data['merchant_id'] = $mercid;
    
    $privatekey = functions::encrypt_sha($username.":|:".$password, $secret);
    $checksumReq = functions::checksumcal($data);
    $dataJson = json_encode($data);
    $request_data = functions::aes256encrypt($dataJson, 'aes-256-cbc', $username, $password);
    
    echo "<h2>Complete Working Test</h2>";
    echo "<p>Token: " . $accessToken . "</p>";
    echo "<p>Order ID: " . $data['orderid'] . "</p>";
    
    // Auto-submit form
    echo '<form id="paymentForm" action="' . $URL . '" method="POST">';
    echo '<input type="hidden" name="privatekey" value="' . $privatekey . '">';
    echo '<input type="hidden" name="merchant_id" value="' . $mercid . '">';
    echo '<input type="hidden" name="apyVer" value="">';
    echo '<input type="hidden" name="encdata" value="' . $request_data . '">';
    echo '<input type="hidden" name="checksum" value="' . $checksumReq . '">';
    echo '<input type="hidden" name="chmod" value="">';
    echo '<button type="submit">Complete Test Payment</button>';
    echo '</form>';
    
    echo '<script>
    setTimeout(function() {
        document.getElementById("paymentForm").submit();
    }, 3000);
    </script>';
    
} else {
    echo "Token generation failed";
}
?>