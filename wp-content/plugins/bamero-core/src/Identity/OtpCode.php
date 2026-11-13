<?php
declare(strict_types=1);

namespace Bamero\Core\Identity;

use InvalidArgumentException;

final class OtpCode
{
    public const LENGTH = 6;

    public static function generate(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * Store a purpose-bound HMAC, not the short OTP itself.
     * The secret must come from WordPress salts or a dedicated runtime secret.
     */
    public static function hash(string $code, string $secret, OtpPurpose $purpose): string
    {
        if (preg_match('/\A[0-9]{6}\z/', $code) !== 1) {
            throw new InvalidArgumentException('OTP must contain exactly six digits.');
        }
        if (strlen($secret) < 32) {
            throw new InvalidArgumentException('OTP hashing secret must be at least 32 bytes.');
        }

        return hash_hmac('sha256', $purpose->value . ':' . $code, $secret);
    }

    public static function verify(
        string $code,
        string $storedHash,
        string $secret,
        OtpPurpose $purpose
    ): bool {
        if (preg_match('/\A[0-9]{6}\z/', $code) !== 1
            || preg_match('/\A[a-f0-9]{64}\z/', $storedHash) !== 1
            || strlen($secret) < 32
        ) {
            return false;
        }

        $candidate = self::hash($code, $secret, $purpose);
        return hash_equals($storedHash, $candidate);
    }
}
