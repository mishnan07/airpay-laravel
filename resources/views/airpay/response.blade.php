<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Response</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #ddd; }
        .success { color: green; font-weight: bold; }
        .failed { color: red; font-weight: bold; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Response</h2>
        
        @if(isset($error))
            <div class="error">
                <strong>Error:</strong> {{ $error }}
            </div>
        @elseif(isset($response))
            @if($response['success'])
                <div class="success">
                    <h3>✓ PAYMENT SUCCESSFUL</h3>
                </div>
            @else
                <div class="failed">
                    <h3>✗ PAYMENT FAILED</h3>
                </div>
            @endif
            
            <table>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td><strong>Transaction ID</strong></td>
                    <td>{{ $response['transaction_id'] }}</td>
                </tr>
                <tr>
                    <td><strong>Airpay Transaction ID</strong></td>
                    <td>{{ $response['ap_transaction_id'] }}</td>
                </tr>
                <tr>
                    <td><strong>Amount</strong></td>
                    <td>{{ $response['amount'] }}</td>
                </tr>
                <tr>
                    <td><strong>Status Code</strong></td>
                    <td>{{ $response['status'] }}</td>
                </tr>
                <tr>
                    <td><strong>Message</strong></td>
                    <td>{{ $response['message'] }}</td>
                </tr>
                @if($response['custom_var'])
                <tr>
                    <td><strong>Custom Variable</strong></td>
                    <td>{{ $response['custom_var'] }}</td>
                </tr>
                @endif
            </table>
        @endif
        
        <a href="{{ route('airpay.form') }}" class="back-btn">Make Another Payment</a>
    </div>
</body>
</html>