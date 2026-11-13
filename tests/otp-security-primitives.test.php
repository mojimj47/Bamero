<?php
declare(strict_types=1);

require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/OtpPurpose.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/OtpCode.php';

use Bamero\Core\Identity\OtpCode;
use Bamero\Core\Identity\OtpPurpose;

function otp_assert(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

$code = OtpCode::generate();
otp_assert(preg_match('/\A[0-9]{6}\z/', $code) === 1, 'generates a six-digit numeric OTP');

$secret = 'unit-test-secret-with-at-least-32-bytes';
$hash = OtpCode::hash($code, $secret, OtpPurpose::CustomerLogin);
otp_assert($hash !== $code, 'stores a keyed digest rather than the raw OTP');
otp_assert(preg_match('/\A[a-f0-9]{64}\z/', $hash) === 1, 'stores a fixed-length SHA-256 HMAC');
otp_assert(OtpCode::verify($code, $hash, $secret, OtpPurpose::CustomerLogin), 'verifies the correct OTP');
otp_assert(!OtpCode::verify('000000', $hash, $secret, OtpPurpose::CustomerLogin), 'rejects an incorrect OTP');
otp_assert(!OtpCode::verify('12345', $hash, $secret, OtpPurpose::CustomerLogin), 'rejects malformed OTP input');
otp_assert(!OtpCode::verify($code, $hash, $secret, OtpPurpose::CustomerRegistration), 'binds OTP to its purpose');
otp_assert(!OtpCode::verify($code, $hash, 'different-secret-at-least-32-bytes', OtpPurpose::CustomerLogin), 'rejects a different server secret');
otp_assert(!OtpCode::verify('123456', '', $secret, OtpPurpose::CustomerLogin), 'rejects empty stored hash');
otp_assert(!OtpCode::verify('123456', $hash, 'short', OtpPurpose::CustomerLogin), 'rejects weak server secret');

echo "OTP security primitive tests passed.\n";
