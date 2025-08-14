<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Airpay Payment Gateway</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,700;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .row { display: flex; gap: 20px; flex-wrap: wrap; }
        .col { flex: 1; min-width: 250px; }
        .red { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Airpay Payment Gateway</h2>
        
        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('airpay.process') }}" method="POST" id="paymentForm">
            @csrf
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Buyer Email <span class="red">*</span></label>
                        <input type="email" name="buyerEmail" value="{{ old('buyerEmail') }}" maxlength="50" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Buyer Phone <span class="red">*</span></label>
                        <input type="text" name="buyerPhone" value="{{ old('buyerPhone') }}" maxlength="15" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>First Name <span class="red">*</span></label>
                        <input type="text" name="buyerFirstName" value="{{ old('buyerFirstName') }}" maxlength="50" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Last Name <span class="red">*</span></label>
                        <input type="text" name="buyerLastName" value="{{ old('buyerLastName') }}" maxlength="50" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="buyerAddress" value="{{ old('buyerAddress') }}" maxlength="255">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="buyerCity" value="{{ old('buyerCity') }}" maxlength="50">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="buyerState" value="{{ old('buyerState') }}" maxlength="50">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="buyerCountry" value="{{ old('buyerCountry') }}" maxlength="50">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="buyerPinCode" value="{{ old('buyerPinCode') }}" maxlength="8">
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Order ID <span class="red">*</span></label>
                        <input type="text" name="orderid" value="{{ old('orderid') }}" maxlength="20" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Amount <span class="red">*</span></label>
                        <input type="text" name="amount" value="{{ old('amount') }}" placeholder="99.50" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Currency <span class="red">*</span></label>
                        <input type="text" name="currency" value="356" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>ISO Currency <span class="red">*</span></label>
                        <input type="text" name="isocurrency" value="INR" readonly>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn">Pay Now</button>
            </div>
        </form>
    </div>
</body>
</html>