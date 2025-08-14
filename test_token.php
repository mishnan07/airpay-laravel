<?php
// Test token generation exactly like working PHP project

class functions
{
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

    //calculates checksum for post data
    public static function checksumcal($postData)
    {
        ksort($postData);
        $data = '';
        foreach ($postData as $key => $value) {
            $data .= $value;
        }
        return self::calculateChecksumHelper($data . date('Y-m-d'));
    }
 
    private static function calculateChecksumHelper($data)
    {
        $checksum = self::makeEnc($data);
        return $checksum;
    }
    //generates a SHA256 hash.
    private static function makeEnc($data)
    {
        $key = hash('SHA256', $data);
        return $key;
    }

    //send the post data with the checksum to a specified URL using cURL, and then retrieve the response
    public static function sendPostData($tokenUrl, $postData)
    {
        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }
}

// Config
$mercid = "335854";
$username = "CKFzeZGut2";
$password = "WRx4M373";
$client_id = "e38135";
$client_secret = "30af9b1913d535990a3c39138f4353c0";
$tokenUrl = "https://kraken.airpay.co.in/airpay/pay/v4/api/oauth2/token.php";

// Token request exactly like working PHP
$request = array();
$request['client_id'] = $client_id;
$request['client_secret'] = $client_secret;
$request['grant_type'] = 'client_credentials';
$request['merchant_id'] = $mercid;

$secretKey = md5($username ."~:~" .$password);

echo "<h2>Token Generation Test</h2>";
echo "<h3>Request Data:</h3><pre>" . json_encode($request, JSON_PRETTY_PRINT) . "</pre>";
echo "<h3>Secret Key:</h3><pre>" . $secretKey . "</pre>";

$encre = functions::encrypt(json_encode($request), $secretKey);

$req = [
    'merchant_id' => $request['merchant_id'],
    "encdata" => $encre,
    "checksum" => functions::checksumcal($request)
];

echo "<h3>Token Request:</h3><pre>" . json_encode($req, JSON_PRETTY_PRINT) . "</pre>";

$access_token = functions::sendPostData($tokenUrl, $req);
echo "<h3>Raw Token Response:</h3><pre>" . $access_token . "</pre>";

$decryptData = functions::decrypt(json_decode($access_token, true), $secretKey);
echo "<h3>Decrypted Token Response:</h3><pre>" . $decryptData . "</pre>";

$tokenResponse = json_decode($decryptData, true);
echo "<h3>Parsed Token Response:</h3><pre>" . json_encode($tokenResponse, JSON_PRETTY_PRINT) . "</pre>";

if (isset($tokenResponse['status']) && $tokenResponse['status'] === 'success') {
    $accessToken = $tokenResponse['data']['access_token'];
    echo "<h3>✅ Token Generated Successfully:</h3><pre>" . $accessToken . "</pre>";
    
    // Test with token
    echo '<h3>Test Form (With Token):</h3>';
    echo '<form action="https://payments.airpay.co.in/pay/v4/index.php?token=' . $accessToken . '" method="POST">';
    echo '<input type="hidden" name="privatekey" value="test">';
    echo '<input type="hidden" name="merchant_id" value="' . $mercid . '">';
    echo '<input type="hidden" name="encdata" value="test">';
    echo '<input type="hidden" name="checksum" value="test">';
    echo '<button type="submit">Test With Valid Token</button>';
    echo '</form>';
} else {
    echo "<h3>❌ Token Generation Failed</h3>";
    if (isset($tokenResponse['message'])) {
        echo "<p>Error: " . $tokenResponse['message'] . "</p>";
    }
}
?>