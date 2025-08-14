<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to Payment Gateway</title>
</head>
<body>
    <div style="text-align: center; padding: 50px;">
        <?php if (false): ?>
        <?php else: ?>
            <h2>Redirecting to Airpay Payment Gateway...</h2>
            <p>Please wait while we redirect you to the payment page.</p>
            
            <form id="paymentForm" action="<?php echo $redirectData['url']; ?>" method="POST">
                <input type="hidden" name="privatekey" value="<?php echo $redirectData['privatekey']; ?>">
                <input type="hidden" name="merchant_id" value="<?php echo $redirectData['merchant_id']; ?>">
                <input type="hidden" name="encdata" value="<?php echo $redirectData['encdata']; ?>">
                <input type="hidden" name="checksum" value="<?php echo $redirectData['checksum']; ?>">
                <input type="hidden" name="chmod" value="">
                <button type="submit">Continue to Payment</button>
            </form>
            
            <script>
                // Auto-submit form after 3 seconds
                setTimeout(function() {
                    document.getElementById('paymentForm').submit();
                }, 3000);
            </script>
        <?php endif; ?>
    </div>
</body>
</html>