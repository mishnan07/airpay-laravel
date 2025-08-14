# Airpay Laravel Integration

## Setup Instructions

1. **Configure Environment**
   - Update `.env` file with your Airpay credentials
   - Set your database connection details

2. **Install Dependencies** (if using full Laravel)
   ```bash
   composer install
   ```

3. **Run the Application**
   - Start XAMPP Apache server
   - Access: `http://localhost/airpay-laravel/public`

## File Structure

```
airpay-laravel/
├── app/
│   ├── Http/Controllers/AirpayController.php
│   └── Services/AirpayService.php
├── resources/views/airpay/
│   ├── payment-form.blade.php
│   ├── redirect.blade.php
│   └── response.blade.php
├── routes/web.php
├── config/app.php
└── .env
```

## Routes

- `GET /` - Payment form
- `POST /process-payment` - Process payment
- `POST /payment-response` - Handle Airpay response

## Test Data

```
Email: test@test.com
Phone: 1234567890
First Name: Test
Last Name: User
Address: Test Address
City: Mumbai
State: Maharashtra
Country: India
Pincode: 400001
Order ID: TEST001
Amount: 100.00
```

## Environment Variables

Update `.env` with your Airpay credentials:
- AIRPAY_MERCHANT_ID
- AIRPAY_USERNAME
- AIRPAY_PASSWORD
- AIRPAY_SECRET
- AIRPAY_CLIENT_ID
- AIRPAY_CLIENT_SECRET