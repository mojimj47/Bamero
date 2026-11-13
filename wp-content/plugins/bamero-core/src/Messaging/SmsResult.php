<?php
declare(strict_types=1);

namespace Bamero\Core\Messaging;

final class SmsResult
{
    private function __construct(
        public readonly bool $success,
        public readonly ?string $messageId,
        public readonly ?string $errorCode
    ) {}

    public static function success(string $messageId): self
    {
        return new self(true, $messageId, null);
    }

    public static function failure(string $errorCode): self
    {
        return new self(false, null, $errorCode);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
