<?php
declare(strict_types=1);

namespace Bamero\Core\Identity;

use InvalidArgumentException;
use RuntimeException;

final class OtpChallengeStore
{
    private string $challengeTable;
    private string $rateLimitTable;

    public function __construct(
        private object $db,
        private readonly IdentityHasher $identityHasher
    )
    {
        if (!isset($db->prefix) || !is_string($db->prefix) || $db->prefix === '') {
            throw new InvalidArgumentException('A valid WordPress database prefix is required.');
        }

        $this->challengeTable = $db->prefix . 'bamero_otp_challenges';
        $this->rateLimitTable = $db->prefix . 'bamero_rate_limits';
    }

    public function issue(
        string $phone,
        OtpPurpose $purpose,
        string $codeHash,
        int $ttlSeconds,
        ?string $ip = null
    ): int {
        $phoneHash = $this->identityHasher->phoneHash($phone);
        $ipHash = $ip !== null ? $this->identityHasher->ipHash($ip) : null;
        self::assertHash($codeHash, 'OTP');
        if ($ttlSeconds < 60 || $ttlSeconds > 900) {
            throw new InvalidArgumentException('OTP lifetime must be between 60 and 900 seconds.');
        }

        $lockName = 'bamero_otp_' . substr(hash('sha256', $phoneHash . ':' . $purpose->value), 0, 48);
        $lockAcquired = (int) $this->db->get_var($this->db->prepare(
            'SELECT GET_LOCK(%s, 5)',
            $lockName
        ));

        if ($lockAcquired !== 1) {
            throw new RuntimeException('Unable to acquire OTP issuance lock.');
        }

        try {
            if ($this->db->query('START TRANSACTION') === false) {
                throw new RuntimeException('Unable to start OTP challenge transaction.');
            }

            try {
                $invalidated = $this->db->query($this->db->prepare(
                    "UPDATE {$this->challengeTable}
                     SET consumed_at = UTC_TIMESTAMP()
                     WHERE phone_hash = %s AND purpose = %s AND consumed_at IS NULL",
                    $phoneHash,
                    $purpose->value
                ));
                if ($invalidated === false) {
                    throw new RuntimeException('Unable to invalidate previous OTP challenges.');
                }

                $inserted = $this->db->insert(
                    $this->challengeTable,
                    [
                        'phone_hash' => $phoneHash,
                        'purpose' => $purpose->value,
                        'code_hash' => $codeHash,
                        'expires_at' => gmdate('Y-m-d H:i:s', time() + $ttlSeconds),
                        'attempts' => 0,
                        'ip_hash' => $ipHash,
                    ],
                    ['%s', '%s', '%s', '%s', '%d', '%s']
                );
                if ($inserted !== 1 || empty($this->db->insert_id)) {
                    throw new RuntimeException('Unable to create OTP challenge.');
                }

                if ($this->db->query('COMMIT') === false) {
                    throw new RuntimeException('Unable to commit OTP challenge transaction.');
                }
            } catch (\Throwable $error) {
                $this->db->query('ROLLBACK');
                throw $error;
            }
        } finally {
            $released = $this->db->get_var($this->db->prepare('SELECT RELEASE_LOCK(%s)', $lockName));
            if ((int) $released !== 1) {
                throw new RuntimeException('Unable to release OTP issuance lock.');
            }
        }

        return (int) $this->db->insert_id;
    }

    public function verifyAndConsume(
        int $challengeId,
        string $phone,
        string $code,
        string $secret,
        OtpPurpose $purpose,
        int $maxAttempts = 5
    ): bool {
        if ($challengeId < 1 || $maxAttempts < 1 || $maxAttempts > 10) {
            return false;
        }
        $phoneHash = $this->identityHasher->phoneHash($phone);

        $row = $this->db->get_row($this->db->prepare(
            "SELECT id, purpose, code_hash
             FROM {$this->challengeTable}
             WHERE id = %d
               AND phone_hash = %s
               AND consumed_at IS NULL
               AND expires_at > UTC_TIMESTAMP()
               AND attempts < %d
             LIMIT 1",
            $challengeId,
            $phoneHash,
            $maxAttempts
        ), ARRAY_A);

        if (!is_array($row) || ($row['purpose'] ?? null) !== $purpose->value) {
            return false;
        }

        $storedHash = (string) ($row['code_hash'] ?? '');
        if (!OtpCode::verify($code, $storedHash, $secret, $purpose)) {
            $attemptRecorded = $this->db->query($this->db->prepare(
                "UPDATE {$this->challengeTable}
                 SET attempts = attempts + 1
                 WHERE id = %d
                   AND phone_hash = %s
                   AND consumed_at IS NULL
                   AND expires_at > UTC_TIMESTAMP()
                   AND attempts < %d",
                $challengeId,
                $phoneHash,
                $maxAttempts
            ));
            if ($attemptRecorded === false) {
                throw new RuntimeException('Unable to record failed OTP attempt.');
            }
            return false;
        }

        $consumed = $this->db->query($this->db->prepare(
            "UPDATE {$this->challengeTable}
             SET consumed_at = UTC_TIMESTAMP()
             WHERE id = %d
               AND phone_hash = %s
               AND purpose = %s
               AND code_hash = %s
               AND consumed_at IS NULL
               AND expires_at > UTC_TIMESTAMP()
               AND attempts < %d",
            $challengeId,
            $phoneHash,
            $purpose->value,
            $storedHash,
            $maxAttempts
        ));

        // Conditional UPDATE makes successful consumption single-use under concurrency.
        return $consumed === 1;
    }

    public function hitPhoneRateLimit(string $phone, string $policy, int $maxHits, int $windowSeconds): bool
    {
        $bucketHash = $this->identityHasher->phoneRateLimitBucket($phone, $policy);
        return $this->hitRateLimit($bucketHash, $maxHits, $windowSeconds);
    }

    public function hitIpRateLimit(string $ip, string $policy, int $maxHits, int $windowSeconds): bool
    {
        $bucketHash = $this->identityHasher->ipRateLimitBucket($ip, $policy);
        return $this->hitRateLimit($bucketHash, $maxHits, $windowSeconds);
    }

    private function hitRateLimit(string $bucketHash, int $maxHits, int $windowSeconds): bool
    {
        self::assertHash($bucketHash, 'Rate-limit bucket');
        if ($maxHits < 1 || $maxHits > 1000 || $windowSeconds < 1 || $windowSeconds > 86400) {
            throw new InvalidArgumentException('Rate-limit bounds are invalid.');
        }

        $sql = $this->db->prepare(
            "INSERT INTO {$this->rateLimitTable} (bucket_key, hit_count, window_started_at)
             VALUES (%s, 1, UTC_TIMESTAMP())
             ON DUPLICATE KEY UPDATE
               hit_count = IF(
                   window_started_at <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL %d SECOND),
                   1,
                   hit_count + 1
               ),
               window_started_at = IF(
                   window_started_at <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL %d SECOND),
                   UTC_TIMESTAMP(),
                   window_started_at
               ),
               updated_at = UTC_TIMESTAMP()",
            $bucketHash,
            $windowSeconds,
            $windowSeconds
        );

        if ($this->db->query($sql) === false) {
            throw new RuntimeException('Unable to update OTP rate-limit bucket.');
        }

        $storedHits = $this->db->get_var($this->db->prepare(
            "SELECT hit_count FROM {$this->rateLimitTable} WHERE bucket_key = %s",
            $bucketHash
        ));
        if (!is_numeric($storedHits) || (int) $storedHits < 1) {
            // Fail closed: a failed/missing counter read must never bypass OTP throttling.
            throw new RuntimeException('Unable to read OTP rate-limit bucket.');
        }

        return (int) $storedHits <= $maxHits;
    }

    private static function assertHash(string $value, string $label): void
    {
        if (preg_match('/\A[a-f0-9]{64}\z/', $value) !== 1) {
            throw new InvalidArgumentException($label . ' hash must be a 64-character lowercase hexadecimal digest.');
        }
    }
}
