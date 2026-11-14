<?php
declare(strict_types=1);

require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/PhoneNumber.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/IdentityHasher.php';

use Bamero\Core\Identity\IdentityHasher;

function identity_hash_assert(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

putenv('BAMERO_IDENTITY_HASH_SECRET=environment-identity-secret-with-32-bytes');
$environmentHasher = IdentityHasher::fromEnvironment();
identity_hash_assert(
    $environmentHasher->phoneHash('09123456789') === (new IdentityHasher('environment-identity-secret-with-32-bytes'))->phoneHash('09123456789'),
    'loads the dedicated hashing secret from the runtime environment'
);
putenv('BAMERO_IDENTITY_HASH_SECRET');

$missingSecretRejected = false;
try {
    IdentityHasher::fromEnvironment();
} catch (InvalidArgumentException) {
    $missingSecretRejected = true;
}
identity_hash_assert($missingSecretRejected, 'fails closed when the dedicated runtime secret is missing');

$hasher = new IdentityHasher('identity-hash-test-secret-with-at-least-32-bytes');
$phoneHash = $hasher->phoneHash('09123456789');
identity_hash_assert(preg_match('/\A[a-f0-9]{64}\z/', $phoneHash) === 1, 'returns a fixed-length HMAC digest');
identity_hash_assert($phoneHash === $hasher->phoneHash('+989123456789'), 'normalizes equivalent Iranian phone formats before hashing');
identity_hash_assert($phoneHash === $hasher->phoneHash('۰۹۱۲۳۴۵۶۷۸۹'), 'normalizes Persian digits before hashing');
identity_hash_assert($phoneHash !== hash('sha256', '+989123456789'), 'does not use an unkeyed phone digest');
identity_hash_assert($phoneHash !== (new IdentityHasher('different-identity-secret-with-32-bytes'))->phoneHash('09123456789'), 'changes when the dedicated secret changes');

$ipHash = $hasher->ipHash('192.0.2.10');
identity_hash_assert(preg_match('/\A[a-f0-9]{64}\z/', $ipHash) === 1, 'returns a fixed-length IP HMAC digest');
identity_hash_assert($ipHash !== $hasher->ipHash('192.0.2.11'), 'distinguishes different IP addresses');
identity_hash_assert($ipHash !== $phoneHash, 'separates phone and IP hash domains');

$phoneBucket = $hasher->phoneRateLimitBucket('09123456789', 'cooldown');
identity_hash_assert($phoneBucket === $hasher->phoneRateLimitBucket('+989123456789', 'cooldown'), 'normalizes phone rate-limit keys');
identity_hash_assert($phoneBucket !== $hasher->phoneRateLimitBucket('09123456789', 'hourly'), 'separates rate-limit policies');
identity_hash_assert($phoneBucket !== $phoneHash, 'separates rate-limit and identity hash domains');

$ipBucket = $hasher->ipRateLimitBucket('192.0.2.10', 'minute');
identity_hash_assert($ipBucket !== $ipHash, 'separates IP rate-limit and identity hash domains');

$invalidPhoneRejected = false;
try {
    $hasher->phoneHash('02112345678');
} catch (InvalidArgumentException) {
    $invalidPhoneRejected = true;
}
identity_hash_assert($invalidPhoneRejected, 'rejects non-mobile phone identity');

$invalidIpRejected = false;
try {
    $hasher->ipHash('not-an-ip-address');
} catch (InvalidArgumentException) {
    $invalidIpRejected = true;
}
identity_hash_assert($invalidIpRejected, 'rejects invalid IP addresses');

$invalidPolicyRejected = false;
try {
    $hasher->phoneRateLimitBucket('09123456789', 'bad policy');
} catch (InvalidArgumentException) {
    $invalidPolicyRejected = true;
}
identity_hash_assert($invalidPolicyRejected, 'rejects unsafe rate-limit policy identifiers');

$weakSecretRejected = false;
try {
    new IdentityHasher('too-short');
} catch (InvalidArgumentException) {
    $weakSecretRejected = true;
}
identity_hash_assert($weakSecretRejected, 'rejects a weak identity hashing secret');

echo "Identity HMAC hashing tests passed.\n";
