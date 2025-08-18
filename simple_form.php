<!DOCTYPE html>
<html>
<head>
    <title>Airpay Payment - Simple Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Airpay Payment Form (Simple)</h2>
    
    <form action="process_simple.php" method="POST">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="buyerEmail" value="test@test.com" required>
        </div>
        
        <div class="form-group">
            <label>Phone:</label>
            <input type="text" name="buyerPhone" value="1234567890" required>
        </div>
        
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="buyerFirstName" value="Test" required>
        </div>
        
        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="buyerLastName" value="User" required>
        </div>
        
        <div class="form-group">
            <label>Address:</label>
            <input type="text" name="buyerAddress" value="Test Address">
        </div>
        
        <div class="form-group">
            <label>City:</label>
            <input type="text" name="buyerCity" value="Mumbai">
        </div>
        
        <div class="form-group">
            <label>State:</label>
            <input type="text" name="buyerState" value="Maharashtra">
        </div>
        
        <div class="form-group">
            <label>Country:</label>
            <input type="text" name="buyerCountry" value="India">
        </div>
        
        <div class="form-group">
            <label>Pincode:</label>
            <input type="text" name="buyerPinCode" value="400001">
        </div>
        
        <div class="form-group">
            <label>Amount:</label>
            <input type="text" name="amount" value="100.00" required>
        </div>
        
        <div class="form-group">
            <label>Order ID:</label>
            <input type="text" name="orderid" value="<?php echo 'TEST' . time(); ?>" required>
        </div>
        
        <input type="hidden" name="currency" value="356">
        <input type="hidden" name="isocurrency" value="INR">
        
        <button type="submit">Pay Now</button>
    </form>
</body>
</html>