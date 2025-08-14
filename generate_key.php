<?php
// Generate Laravel APP_KEY
$key = 'base64:' . base64_encode(random_bytes(32));
echo "Generated APP_KEY: " . $key . PHP_EOL;
echo PHP_EOL;
echo "Copy this key and replace 'base64:your-app-key-here' in your .env file";
?>