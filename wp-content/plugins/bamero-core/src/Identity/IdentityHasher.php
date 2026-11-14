<?php
declare(strict_types=1);

namespace Bamero\Core\Identity;

use InvalidArgumentException;

final class IdentityHasher
{
    public function __construct(private readonly string $secret)
    {
        if (strlen($secret) < 32) {
            throw new InvalidArgumentException('Identity hashing secret must be at least 32 bytes.');
        }
    }

    public static function fromEnvironment(): self
    {
        $secret = defined('BAMERO_IDENTITY_HASH_SECRET')
            ? constant('BAMERO_IDENTITY_HASH_SECRET')
            : getenv('BAMERO_IDENTITY_HASH_SECRET');

        if (!is_string($secret) || $secret === '') {
            throw new InvalidArgumentException('BAMERO_IDENTITY_HASH_SECRET must be configured outside the database.');
        }

        return new self($secret);
    }

    public function phoneHash(string $phone): string
    {
        $normalized = PhoneNumber::normalize($phone);
        if ($normalized === null) {
            throw new InvalidArgumentException('A valid Iranian mobile number is required.');
        }

        return hash_hmac('sha256', 'identity:phone:' . $normalized, $this->secret);
    }

    public function ipHash(string $ip): string
    {
        $packed = inet_pton($ip);
        if ($packed === false) {
            throw new InvalidArgumentException('A valid IP address is required.');
        }

        return hash_hmac('sha256', 'identity:ip:' . bin2hex($packed), $this->secret);
    }

    public function phoneRateLimitBucket(string $phone, string $policy): string
    {
        self::assertPolicy($policy);
        $normalized = PhoneNumber::normalize($phone);
        if ($normalized === null) {
            throw new InvalidArgumentException('A valid Iranian mobile number is required.');
        }

        return hash_hmac('sha256', 'rate-limit:phone:' . $policy . ':' . $normalized, $this->secret);
    }

    public function ipRateLimitBucket(string $ip, string $policy): string
    {
        self::assertPolicy($policy);
        $packed = inet_pton($ip);
        if ($packed === false) {
            throw new InvalidArgumentException('A valid IP address is required.');
        }

        return hash_hmac('sha256', 'rate-limit:ip:' . $policy . ':' . bin2hex($packed), $this->secret);
    }

    private static function assertPolicy(string $policy): void
    {
        if (preg_match('/\A[a-z][a-z0-9_]{0,31}\z/', $policy) !== 1) {
            throw new InvalidArgumentException('Rate-limit policy identifier is invalid.');
        }
    }
}
