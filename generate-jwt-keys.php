<?php
// Generate JWT keys
$config = [
    'private_key_bits' => 4096,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
];

$res = openssl_pkey_new($config);
if ($res === false) {
    die("Error creating private key: " . openssl_error_string() . "\n");
}

$privateKeyPath = __DIR__ . '/config/jwt/private.pem';
$publicKeyPath = __DIR__ . '/config/jwt/public.pem';

// Export private key
if (!openssl_pkey_export_to_file($res, $privateKeyPath)) {
    die("Error exporting private key: " . openssl_error_string() . "\n");
}

// Export public key
$details = openssl_pkey_get_details($res);
if ($details === false) {
    die("Error getting key details: " . openssl_error_string() . "\n");
}

if (!file_put_contents($publicKeyPath, $details['key'])) {
    die("Error writing public key file\n");
}

echo "JWT keys generated successfully!\n";
echo "Private key: $privateKeyPath\n";
echo "Public key: $publicKeyPath\n";
?>
