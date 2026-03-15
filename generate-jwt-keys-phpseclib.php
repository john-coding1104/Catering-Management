<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use phpseclib3\Crypt\RSA;

$jwtDir = __DIR__ . '/config/jwt';
$privateKeyPath = $jwtDir . '/private.pem';
$publicKeyPath = $jwtDir . '/public.pem';

if (!is_dir($jwtDir) && !mkdir($jwtDir, 0777, true) && !is_dir($jwtDir)) {
    fwrite(STDERR, "Failed to create directory: {$jwtDir}\n");
    exit(1);
}

$private = RSA::createKey(4096);
$public = $private->getPublicKey();

$privatePem = $private->toString('PKCS8');
$publicPem = $public->toString('PKCS8');

if (file_put_contents($privateKeyPath, $privatePem) === false) {
    fwrite(STDERR, "Failed to write private key: {$privateKeyPath}\n");
    exit(1);
}

if (file_put_contents($publicKeyPath, $publicPem) === false) {
    fwrite(STDERR, "Failed to write public key: {$publicKeyPath}\n");
    exit(1);
}

echo "JWT keys generated successfully (phpseclib).\n";
echo "Private key: {$privateKeyPath}\n";
echo "Public key: {$publicKeyPath}\n";

