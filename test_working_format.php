<?php
// Copy exact functions from working PHP project

class functions
{
    public static function aes256encrypt($data, $cipher , $username, $password)//cipher-encryption algorithm
    {
        $key            =   md5($username . "~:~" . $password);
        $iv             =   bin2hex(openssl_random_pseudo_bytes(8));
        $encrypted      =   openssl_encrypt($data, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $encryptedData  =   base64_encode($encrypted);
        return $iv . $encryptedData;
    }

    public static function encrypt_sha($data, $salt) {
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('SHA256', $salt.'@'.$data);
        return $key;
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
}

// Exact config from working project
$mercid = "335854";
$username = "CKFzeZGut2";
$password = "WRx4M373";
$secret = "V8GqK8T6RC4ajHM8";

// Test data exactly like working project would receive
$data = [];
$data['buyer_email'] = 'test@test.com';
$data['buyer_phone'] = '1234567890';
$data['buyer_firstname'] = 'Test';
$data['buyer_lastname'] = 'User';
$data['buyer_address'] = 'Test Address';
$data['amount'] = '100.00';
$data['buyer_city'] = 'Mumbai';
$data['buyer_state'] = 'Maharashtra';
$data['buyer_pincode'] = '400001';
$data['buyer_country'] = 'India';
$data['orderid'] = 'TEST001';
$data['currency'] = '356';
$data['currency_code'] = '356';
$data['iso_currency'] = 'INR';
$data['merchant_id'] = $mercid;

// Generate data exactly like working project
$privatekey = functions::encrypt_sha($username.":|:".$password, $secret);
$checksumReq = functions::checksumcal($data);
$dataJson = json_encode($data);
$request_data = functions::aes256encrypt($dataJson, 'aes-256-cbc', $username, $password);

echo "<h2>Working PHP Project Format</h2>";
echo "<h3>Data JSON:</h3><pre>" . $dataJson . "</pre>";
echo "<h3>Private Key:</h3><pre>" . $privatekey . "</pre>";
echo "<h3>Checksum:</h3><pre>" . $checksumReq . "</pre>";
echo "<h3>Encrypted Data:</h3><pre>" . substr($request_data, 0, 100) . "...</pre>";

// Test form with exact working format
echo '<h3>Test Form (Exact Working Format):</h3>';
echo '<form action="https://payments.airpay.co.in/pay/v4/index.php" method="POST">';
echo '<input type="hidden" name="privatekey" value="' . $privatekey . '">';
echo '<input type="hidden" name="merchant_id" value="' . $mercid . '">';
echo '<input type="hidden" name="apyVer" value="">';
echo '<input type="hidden" name="encdata" value="' . $request_data . '">';
echo '<input type="hidden" name="checksum" value="' . $checksumReq . '">';
echo '<input type="hidden" name="chmod" value="">';
echo '<button type="submit">Test Exact Working Format</button>';
echo '</form>';
?>