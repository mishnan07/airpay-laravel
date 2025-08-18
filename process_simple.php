<?php
// Process form using exact working PHP code

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
    
    // Payment data from form
    $data = [];
    $data['buyer_email'] = trim($_POST['buyerEmail']);
    $data['buyer_phone'] = trim($_POST['buyerPhone']);
    $data['buyer_firstname'] = trim($_POST['buyerFirstName']);
    $data['buyer_lastname'] = trim($_POST['buyerLastName']);
    $data['buyer_address'] = trim($_POST['buyerAddress']);
    $data['buyer_city'] = trim($_POST['buyerCity']);
    $data['buyer_state'] = trim($_POST['buyerState']);
    $data['buyer_country'] = trim($_POST['buyerCountry']);
    $data['buyer_pincode'] = trim($_POST['buyerPinCode']);
    $data['amount'] = trim($_POST['amount']);
    $data['orderid'] = trim($_POST['orderid']);
    $data['currency'] = trim($_POST['currency']);
    $data['currency_code'] = trim($_POST['currency']);
    $data['iso_currency'] = trim($_POST['isocurrency']);
    $data['merchant_id'] = $mercid;
    
    $privatekey = functions::encrypt_sha($username.":|:".$password, $secret);
    $checksumReq = functions::checksumcal($data);
    $dataJson = json_encode($data);
    $request_data = functions::aes256encrypt($dataJson, 'aes-256-cbc', $username, $password);
    
    // Auto-submit form
    echo '<!DOCTYPE html>
    <html>
    <head><title>Redirecting to Airpay</title></head>
    <body onload="document.forms[0].submit();">
    <center>
    <p>Redirecting to Airpay Payment Gateway...</p>
    <form action="' . $URL . '" method="POST">
        <input type="hidden" name="privatekey" value="' . $privatekey . '">
        <input type="hidden" name="merchant_id" value="' . $mercid . '">
        <input type="hidden" name="apyVer" value="">
        <input type="hidden" name="encdata" value="' . $request_data . '">
        <input type="hidden" name="checksum" value="' . $checksumReq . '">
        <input type="hidden" name="chmod" value="">
        <button type="submit">Continue</button>
    </form>
    </center>
    </body>
    </html>';
    
} else {
    echo "Token generation failed";
}
?>