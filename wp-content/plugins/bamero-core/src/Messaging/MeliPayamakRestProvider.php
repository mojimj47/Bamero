<?php
declare(strict_types=1);

namespace Bamero\Core\Messaging;

use Bamero\Core\Identity\PhoneNumber;

final class MeliPayamakRestProvider implements SmsProvider, OtpSmsProvider
{
    private const SEND_ENDPOINT = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';
    private const OTP_ENDPOINT = 'https://rest.payamak-panel.com/api/SendSMS/SendOtp';
    private const TIMEOUT = 15;

    public function __construct(
        private readonly string $username,
        private readonly string $apiKey,
        private readonly string $sender
    ) {}

    public function send(string $recipient, string $message): SmsResult
    {
        $message = trim($message);
        if (!$this->isConfigured()) {
            return SmsResult::failure('not_configured');
        }
        if ($message === '' || strlen($message) > 4000) {
            return SmsResult::failure('invalid_message');
        }

        $localRecipient = $this->localRecipient($recipient);
        if ($localRecipient === null) {
            return SmsResult::failure('invalid_recipient');
        }

        return $this->request(self::SEND_ENDPOINT, [
            'username' => $this->username,
            'password' => $this->apiKey,
            'to' => $localRecipient,
            'from' => $this->sender,
            'text' => $message,
            'isFlash' => 'false',
        ]);
    }

    public function sendOtpCode(string $recipient, string $code): SmsResult
    {
        if (!$this->isConfigured()) {
            return SmsResult::failure('not_configured');
        }
        if (preg_match('/\A[0-9]{6}\z/', $code) !== 1) {
            return SmsResult::failure('invalid_otp_code');
        }

        $localRecipient = $this->localRecipient($recipient);
        if ($localRecipient === null) {
            return SmsResult::failure('invalid_recipient');
        }

        return $this->request(self::OTP_ENDPOINT, [
            'username' => $this->username,
            'password' => $this->apiKey,
            'to' => $localRecipient,
            'from' => $this->sender,
            'code' => $code,
        ]);
    }

    private function isConfigured(): bool
    {
        return $this->username !== '' && $this->apiKey !== '' && $this->sender !== '';
    }

    private function localRecipient(string $recipient): ?string
    {
        $normalized = PhoneNumber::normalize($recipient);
        return $normalized === null ? null : '0' . substr($normalized, 3);
    }

    private function request(string $endpoint, array $body): SmsResult
    {
        if (!function_exists('wp_remote_post')) {
            return SmsResult::failure('wordpress_http_unavailable');
        }

        $response = wp_remote_post($endpoint, [
            'timeout' => self::TIMEOUT,
            'redirection' => 0,
            'blocking' => true,
            'sslverify' => true,
            'limit_response_size' => 4096,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ],
            'body' => $body,
        ]);

        if (is_wp_error($response)) {
            return SmsResult::failure('transport_error');
        }

        $httpStatus = wp_remote_retrieve_response_code($response);
        if ($httpStatus < 200 || $httpStatus >= 300) {
            return SmsResult::failure('http_' . $httpStatus);
        }

        $raw = trim(wp_remote_retrieve_body($response));
        $payload = json_decode($raw, true);
        if (is_array($payload) && array_key_exists('RetStatus', $payload) && array_key_exists('Value', $payload)) {
            $value = trim((string) $payload['Value']);
            if ((string) $payload['RetStatus'] !== '1') {
                return SmsResult::failure(preg_match('/\A-?\d+\z/', $value) === 1 ? $value : 'provider_rejected');
            }
        } else {
            $value = trim($raw, " \t\n\r\0\x0B\"'");
        }

        if ($value === '' || !ctype_digit($value)) {
            return SmsResult::failure(preg_match('/\A-?\d+\z/', $value) === 1 ? $value : 'provider_rejected');
        }

        return SmsResult::success($value);
    }
}
