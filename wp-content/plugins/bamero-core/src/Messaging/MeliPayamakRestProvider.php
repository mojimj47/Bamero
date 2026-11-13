<?php
declare(strict_types=1);

namespace Bamero\Core\Messaging;

use Bamero\Core\Identity\PhoneNumber;

final class MeliPayamakRestProvider implements SmsProvider
{
    private const ENDPOINT = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';
    private const TIMEOUT = 15;

    public function __construct(
        private readonly string $username,
        private readonly string $apiKey,
        private readonly string $sender
    ) {}

    public function send(string $recipient, string $message): SmsResult
    {
        if ($this->username === '' || $this->apiKey === '' || $this->sender === '') {
            return SmsResult::failure('not_configured');
        }

        $normalized = PhoneNumber::normalize($recipient);
        $message = trim($message);

        if ($normalized === null) {
            return SmsResult::failure('invalid_recipient');
        }
        if ($message === '' || strlen($message) > 4000) {
            return SmsResult::failure('invalid_message');
        }
        if (!function_exists('wp_remote_post')) {
            return SmsResult::failure('wordpress_http_unavailable');
        }

        $localRecipient = '0' . substr($normalized, 3);
        $response = wp_remote_post(self::ENDPOINT, [
            'timeout' => self::TIMEOUT,
            'redirection' => 0,
            'blocking' => true,
            'sslverify' => true,
            'limit_response_size' => 4096,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ],
            'body' => [
                'username' => $this->username,
                // The API key is sent in the provider's legacy password field.
                'password' => $this->apiKey,
                'to' => $localRecipient,
                'from' => $this->sender,
                'text' => $message,
                'isFlash' => 'false',
            ],
        ]);

        if (is_wp_error($response)) {
            return SmsResult::failure('transport_error');
        }

        $httpStatus = wp_remote_retrieve_response_code($response);
        if ($httpStatus < 200 || $httpStatus >= 300) {
            return SmsResult::failure('http_' . $httpStatus);
        }

        $payload = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($payload) || !array_key_exists('RetStatus', $payload) || !array_key_exists('Value', $payload)) {
            return SmsResult::failure('invalid_provider_response');
        }

        $value = trim((string) $payload['Value']);
        if ((string) $payload['RetStatus'] !== '1') {
            return SmsResult::failure(preg_match('/\A-?\d+\z/', $value) === 1 ? $value : 'provider_rejected');
        }
        if ($value === '' || !ctype_digit($value)) {
            return SmsResult::failure(preg_match('/\A-?\d+\z/', $value) === 1 ? $value : 'provider_rejected');
        }

        return SmsResult::success($value);
    }
}
