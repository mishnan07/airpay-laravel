<?php

namespace App\Services;

// No imports needed - using global Http class

class AirpayService
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'mercid' => env('AIRPAY_MERCHANT_ID', '335854'),
            'username' => env('AIRPAY_USERNAME', 'CKFzeZGut2'),
            'password' => env('AIRPAY_PASSWORD', 'WRx4M373'),
            'secret' => env('AIRPAY_SECRET', 'V8GqK8T6RC4ajHM8'),
            'client_id' => env('AIRPAY_CLIENT_ID', 'e38135'),
            'client_secret' => env('AIRPAY_CLIENT_SECRET', '30af9b1913d535990a3c39138f4353c0'),
            'token_url' => env('AIRPAY_TOKEN_URL', 'https://kraken.airpay.co.in/airpay/pay/v4/api/oauth2/token.php'),
            'payment_url' => env('AIRPAY_PAYMENT_URL', 'https://payments.airpay.co.in/pay/v4/index.php')
        ];
    }

    public function initiatePayment($data)
    {
        // Generate fresh token every time (like working test)
        $accessToken = $this->getAccessToken();
        $paymentUrl = $this->config['payment_url'] . '?token=' . $accessToken;
        
        // Prepare payment data
        $paymentData = $this->preparePaymentData($data);
        
        // Generate checksum and encrypt data using exact working methods
        $encryptedData = $this->aes256encrypt(json_encode($paymentData), 'aes-256-cbc', $this->config['username'], $this->config['password']);
        $checksum = $this->checksumcal($paymentData);
        $privateKey = $this->encrypt_sha($this->config['username'] . ":|:" . $this->config['password'], $this->config['secret']);
        
        return [
            'url' => $paymentUrl,
            'privatekey' => $privateKey,
            'merchant_id' => $this->config['mercid'],
            'encdata' => $encryptedData,
            'checksum' => $checksum
        ];
    }
    
    // Copy exact working functions
    private function aes256encrypt($data, $cipher, $username, $password)
    {
        $key = md5($username . "~:~" . $password);
        $iv = bin2hex(openssl_random_pseudo_bytes(8));
        $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $encryptedData = base64_encode($encrypted);
        return $iv . $encryptedData;
    }
    
    private function encrypt_sha($data, $salt)
    {
        return hash('SHA256', $salt . '@' . $data);
    }
    
    private function checksumcal($postData)
    {
        ksort($postData);
        $data = '';
        foreach ($postData as $key => $value) {
            $data .= $value;
        }
        return hash('SHA256', $data . date('Y-m-d'));
    }

    public function handlePaymentResponse($postData)
    {
        if (!isset($postData['response']) || empty($postData['response'])) {
            // Handle direct JSON error response
            $jsonData = json_decode($postData['response'] ?? '{}', true);
            if (isset($jsonData['success']) && !$jsonData['success']) {
                throw new \Exception('Payment failed: ' . strip_tags($jsonData['message']));
            }
            throw new \Exception('Response is empty');
        }

        $secretKey = md5($this->config['username'] . "~:~" . $this->config['password']);
        $decryptedData = $this->decryptString($postData['response'], $secretKey);
        
        if (empty($decryptedData)) {
            throw new \Exception('Decryption failed');
        }

        $responseData = json_decode($decryptedData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response');
        }

        return $this->validateAndFormatResponse($responseData);
    }

    private function getAccessToken()
    {
        $secretKey = md5($this->config['username'] . "~:~" . $this->config['password']);
        
        $request = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'grant_type' => 'client_credentials',
            'merchant_id' => $this->config['mercid']
        ];

        $encryptedRequest = $this->encrypt(json_encode($request), $secretKey);
        
        $postData = [
            'merchant_id' => $this->config['mercid'],
            'encdata' => $encryptedRequest,
            'checksum' => $this->generateChecksum($request)
        ];

        // Use cURL like the working PHP code
        $ch = curl_init($this->config['token_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        if (!$responseData) {
            throw new \Exception('Empty response from token API');
        }
        
        $decryptedResponse = $this->decrypt($responseData, $secretKey);
        $tokenData = json_decode($decryptedResponse, true);

        if (!isset($tokenData['status']) || $tokenData['status'] !== 'success') {
            $errorMsg = isset($tokenData['message']) ? $tokenData['message'] : 'Unknown token error';
            throw new \Exception('Token generation failed: ' . $errorMsg);
        }
        
        return $tokenData['data']['access_token'];

        return $tokenData['data']['access_token'];
    }

    private function preparePaymentData($data)
    {
        return [
            'buyer_email' => trim($data['buyerEmail']),
            'buyer_phone' => trim($data['buyerPhone']),
            'buyer_firstname' => trim($data['buyerFirstName']),
            'buyer_lastname' => trim($data['buyerLastName']),
            'buyer_address' => trim($data['buyerAddress'] ?? ''),
            'buyer_city' => trim($data['buyerCity'] ?? ''),
            'buyer_state' => trim($data['buyerState'] ?? ''),
            'buyer_country' => trim($data['buyerCountry'] ?? ''),
            'buyer_pincode' => trim($data['buyerPinCode'] ?? ''),
            'amount' => trim($data['amount']),
            'orderid' => trim($data['orderid']),
            'currency' => trim($data['currency'] ?? '356'),
            'currency_code' => trim($data['currency'] ?? '356'),
            'iso_currency' => trim($data['isocurrency'] ?? 'INR'),
            'merchant_id' => $this->config['mercid']
        ];
    }

    private function encryptData($data)
    {
        $key = md5($this->config['username'] . "~:~" . $this->config['password']);
        $iv = bin2hex(openssl_random_pseudo_bytes(8));
        $encrypted = openssl_encrypt(json_encode($data), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $encryptedData = base64_encode($encrypted);
        return $iv . $encryptedData;
    }

    private function encrypt($request, $secretKey)
    {
        $iv = bin2hex(openssl_random_pseudo_bytes(8));
        $raw = openssl_encrypt($request, 'aes-256-cbc', $secretKey, OPENSSL_RAW_DATA, $iv);
        return $iv . base64_encode($raw);
    }

    private function decrypt($requestData, $secretKey)
    {
        $data = $requestData['response'];
        $iv = substr($data, 0, 16);
        $encryptedData = substr($data, 16);
        return openssl_decrypt(base64_decode($encryptedData), 'AES-256-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
    }

    private function decryptString($requestData, $secretKey)
    {
        $iv = substr($requestData, 0, 16);
        $encryptedData = substr($requestData, 16);
        $raw = openssl_decrypt(base64_decode($encryptedData), 'AES-256-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
        return $raw !== false ? $raw : '';
    }

    private function generateChecksum($data)
    {
        ksort($data);
        $string = '';
        foreach ($data as $value) {
            $string .= $value;
        }
        return hash('SHA256', $string . date('Y-m-d'));
    }

    private function generatePrivateKey()
    {
        return hash('SHA256', $this->config['secret'] . '@' . $this->config['username'] . ":|:" . $this->config['password']);
    }

    private function validateAndFormatResponse($data)
    {
        $responseData = $data['data'] ?? $data;
        
        $transactionId = $responseData['orderid'] ?? '';
        $apTransactionId = $responseData['ap_transactionid'] ?? '';
        $amount = $responseData['amount'] ?? '';
        $status = $responseData['transaction_status'] ?? '';
        $message = $responseData['message'] ?? '';
        $secureHash = $responseData['ap_securehash'] ?? '';

        if (empty($transactionId) || empty($apTransactionId) || empty($amount) || empty($status) || empty($secureHash)) {
            throw new \Exception('Required response fields are missing');
        }

        // Verify secure hash
        $merchantHash = sprintf("%u", crc32($transactionId . ':' . $apTransactionId . ':' . $amount . ':' . $status . ':' . $message . ':' . $this->config['mercid'] . ':' . $this->config['username']));
        
        if ($secureHash != $merchantHash) {
            throw new \Exception('Secure hash mismatch');
        }

        return [
            'success' => $status == 200,
            'transaction_id' => $transactionId,
            'ap_transaction_id' => $apTransactionId,
            'amount' => $amount,
            'status' => $status,
            'message' => $message,
            'custom_var' => $responseData['custom_var'] ?? ''
        ];
    }
}