<!DOCTYPE html>
<html>
<head>
    <title>Payment Response</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .success { color: green; }
        .error { color: red; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Response</h2>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <h3>Error:</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php elseif (isset($response)): ?>
            <div class="<?php echo $response['success'] ? 'success' : 'error'; ?>">
                <h3><?php echo $response['success'] ? 'Payment Successful!' : 'Payment Failed'; ?></h3>
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($response['transaction_id']); ?></p>
                <p><strong>Amount:</strong> <?php echo htmlspecialchars($response['amount']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($response['status']); ?></p>
                <p><strong>Message:</strong> <?php echo htmlspecialchars($response['message']); ?></p>
            </div>
        <?php endif; ?>
        
        <p><a href="/airpay-laravel/public/">Make Another Payment</a></p>
    </div>
</body>
</html>