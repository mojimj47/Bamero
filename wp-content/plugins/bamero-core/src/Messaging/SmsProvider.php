<?php
declare(strict_types=1);

namespace Bamero\Core\Messaging;

interface SmsProvider
{
    public function send(string $recipient, string $message): SmsResult;
}
