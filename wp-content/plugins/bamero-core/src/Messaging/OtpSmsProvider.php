<?php
declare(strict_types=1);

namespace Bamero\Core\Messaging;

interface OtpSmsProvider
{
    public function sendOtpCode(string $recipient, string $code): SmsResult;
}
