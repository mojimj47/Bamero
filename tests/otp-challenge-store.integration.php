<?php
declare(strict_types=1);

use Bamero\Core\Identity\OtpChallengeStore;
use Bamero\Core\Identity\OtpCode;
use Bamero\Core\Identity\OtpPurpose;

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'bamero_test';
$user = getenv('DB_USER') ?: 'bamero';
$password = getenv('DB_PASSWORD') ?: 'bamero_test_password';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

if (!defined('ARRAY_A')) {
    define('ARRAY_A', 'ARRAY_A');
}

final class PdoWpdbAdapter
{
    public string $prefix = 'otp_integration_';
    public int $insert_id = 0;
    public bool $failNextGetVar = false;

    public function __construct(private PDO $pdo)
    {
    }

    public function prepare(string $query, mixed ...$args): string
    {
        $index = 0;
        return preg_replace_callback('/%[sd]/', function (array $match) use (&$index, $args): string {
            if (!array_key_exists($index, $args)) {
                throw new RuntimeException('Missing prepared SQL argument.');
            }
            $value = $args[$index++];
            return $match[0] === '%d'
                ? (string) (int) $value
                : $this->pdo->quote((string) $value);
        }, $query);
    }

    public function query(string $sql): int|false
    {
        return $this->pdo->exec($sql);
    }

    public function insert(string $table, array $data, array $format = []): int|false
    {
        $columns = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($columns), '%s'));
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            $placeholders
        );
        $args = array_values($data);
        $this->query($this->prepare($sql, ...$args));
        $this->insert_id = (int) $this->pdo->lastInsertId();
        return 1;
    }

    public function get_row(string $sql, mixed $output = ARRAY_A): array|null
    {
        $statement = $this->pdo->query($sql);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function get_var(string $sql): mixed
    {
        if ($this->failNextGetVar) {
            $this->failNextGetVar = false;
            return null;
        }
        $value = $this->pdo->query($sql)->fetchColumn();
        return $value === false ? null : $value;
    }
}

require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/PhoneNumber.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/IdentityHasher.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/OtpPurpose.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/OtpCode.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/OtpChallengeStore.php';


$schema = file_get_contents(__DIR__ . '/../wp-content/plugins/bamero-core/database/schema.sql');
if ($schema === false) {
    throw new RuntimeException('Unable to read OTP database schema.');
}
$schema = str_replace('{{prefix}}', 'otp_integration_', $schema);
foreach (preg_split('/;\s*(?:\r?\n|$)/', trim($schema)) ?: [] as $statement) {
    if (trim($statement) !== '') {
        $pdo->exec(trim($statement));
    }
}

$db = new PdoWpdbAdapter($pdo);
$identityHasher = new \Bamero\Core\Identity\IdentityHasher(str_repeat('i', 32));
$store = new \Bamero\Core\Identity\OtpChallengeStore($db, $identityHasher);
$secret = str_repeat('s', 32);
$phone = '09123456789';
$phoneHash = $identityHasher->phoneHash($phone);
$ip = '192.0.2.10';
$ipHash = $identityHasher->ipHash($ip);
$correctCode = '481205';
$wrongCode = '111111';
$codeHash = \Bamero\Core\Identity\OtpCode::hash($correctCode, $secret, \Bamero\Core\Identity\OtpPurpose::CustomerLogin);

$lockPdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$lockName = 'bamero_otp_' . substr(hash('sha256', $phoneHash . ':' . OtpPurpose::CustomerLogin->value), 0, 48);
$lockStatement = $lockPdo->prepare('SELECT GET_LOCK(?, 0)');
$lockStatement->execute([$lockName]);
if ((int) $lockStatement->fetchColumn() !== 1) {
    throw new RuntimeException('Unable to acquire the integration-test OTP lock.');
}
$issuanceWasBlocked = false;
try {
    $store->issue($phone, OtpPurpose::CustomerLogin, $codeHash, 300, $ip);
} catch (RuntimeException) {
    $issuanceWasBlocked = true;
} finally {
    $release = $lockPdo->prepare('SELECT RELEASE_LOCK(?)');
    $release->execute([$lockName]);
}
if (!$issuanceWasBlocked) {
    throw new RuntimeException('Concurrent OTP issuance must serialize on a per-phone/purpose database lock.');
}

$first = $store->issue($phone, OtpPurpose::CustomerLogin, $codeHash, 300, $ip);
$persistedIdentity = $db->get_row($db->prepare(
    'SELECT phone_hash, ip_hash FROM otp_integration_bamero_otp_challenges WHERE id = %d',
    $first
));
if (!is_array($persistedIdentity)
    || !hash_equals($phoneHash, (string) $persistedIdentity['phone_hash'])
    || !hash_equals($ipHash, (string) $persistedIdentity['ip_hash'])
    || hash_equals($phone, (string) $persistedIdentity['phone_hash'])
    || hash_equals($ip, (string) $persistedIdentity['ip_hash'])
) {
    throw new RuntimeException('OTP storage must persist keyed identity digests, never raw phone or IP values.');
}
$releaseProbe = $lockPdo->prepare('SELECT GET_LOCK(?, 0)');
$releaseProbe->execute([$lockName]);
if ((int) $releaseProbe->fetchColumn() !== 1) {
    throw new RuntimeException('A successful OTP issuance must release its per-phone/purpose lock.');
}
$releaseProbe = $lockPdo->prepare('SELECT RELEASE_LOCK(?)');
$releaseProbe->execute([$lockName]);

$second = $store->issue($phone, OtpPurpose::CustomerLogin, $codeHash, 300, $ip);
if ($store->verifyAndConsume($first, $phone, $correctCode, $secret, OtpPurpose::CustomerLogin)) {
    throw new RuntimeException('Issuing a new challenge must invalidate the previous challenge.');
}
if (!$store->verifyAndConsume($second, $phone, $correctCode, $secret, OtpPurpose::CustomerLogin)) {
    throw new RuntimeException('A valid, active OTP must be accepted exactly once.');
}
if ($store->verifyAndConsume($second, $phone, $correctCode, $secret, OtpPurpose::CustomerLogin)) {
    throw new RuntimeException('A consumed OTP must not be reusable.');
}
if ($store->verifyAndConsume($second, '09123456788', $correctCode, $secret, OtpPurpose::CustomerLogin)) {
    throw new RuntimeException('A challenge must only be verifiable for its original phone hash.');
}

$wrongAttemptsId = $store->issue($phone, OtpPurpose::CustomerLogin, $codeHash, 300);
for ($attempt = 0; $attempt < 3; $attempt++) {
    if ($store->verifyAndConsume($wrongAttemptsId, $phone, $wrongCode, $secret, OtpPurpose::CustomerLogin, 3)) {
        throw new RuntimeException('An incorrect OTP must never verify.');
    }
}
if ($store->verifyAndConsume($wrongAttemptsId, $phone, $correctCode, $secret, OtpPurpose::CustomerLogin, 3)) {
    throw new RuntimeException('An OTP must be blocked after the configured attempt limit.');
}

$purposeId = $store->issue($phone, OtpPurpose::CustomerLogin, $codeHash, 300);
if ($store->verifyAndConsume($purposeId, $phone, $correctCode, $secret, OtpPurpose::CustomerRegistration)) {
    throw new RuntimeException('OTP purpose must be enforced by the persisted challenge.');
}

$expiredId = $store->issue($phone, OtpPurpose::CustomerLogin, $codeHash, 300);
$db->query($db->prepare(
    'UPDATE otp_integration_bamero_otp_challenges SET expires_at = DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 SECOND) WHERE id = %d',
    $expiredId
));
if ($store->verifyAndConsume($expiredId, $phone, $correctCode, $secret, OtpPurpose::CustomerLogin)) {
    throw new RuntimeException('Expired OTP challenges must be rejected.');
}

if (!$store->hitPhoneRateLimit($phone, 'otp_issue', 2, 60)
    || !$store->hitPhoneRateLimit($phone, 'otp_issue', 2, 60)
    || $store->hitPhoneRateLimit($phone, 'otp_issue', 2, 60)
) {
    throw new RuntimeException('Rate-limit buckets must enforce their configured hit count.');
}

if (!$store->hitIpRateLimit($ip, 'otp_issue_ip', 2, 60)
    || !$store->hitIpRateLimit($ip, 'otp_issue_ip', 2, 60)
    || $store->hitIpRateLimit($ip, 'otp_issue_ip', 2, 60)
) {
    throw new RuntimeException('IP rate-limit buckets must enforce their configured hit count.');
}

$db->failNextGetVar = true;
$rateLimitReadFailedClosed = false;
try {
    $store->hitPhoneRateLimit('09123456788', 'otp_failure_probe', 5, 60);
} catch (RuntimeException) {
    $rateLimitReadFailedClosed = true;
}
if (!$rateLimitReadFailedClosed) {
    throw new RuntimeException('A failed rate-limit counter read must fail closed.');
}

echo "OTP challenge persistence and rate-limit integration tests passed against MariaDB.\n";
