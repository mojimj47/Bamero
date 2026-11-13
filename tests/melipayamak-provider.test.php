<?php
declare(strict_types=1);

$lastRequest = null;
$responseQueue = [];

function wp_remote_post(string $url, array $args)
{
    global $lastRequest, $responseQueue;
    $lastRequest = [$url, $args];
    return array_shift($responseQueue);
}

function is_wp_error($value): bool
{
    return $value instanceof WP_Error;
}

function wp_remote_retrieve_response_code(array $response): int
{
    return (int) ($response['response']['code'] ?? 0);
}

function wp_remote_retrieve_body(array $response): string
{
    return (string) ($response['body'] ?? '');
}

final class WP_Error
{
    public function __construct(public string $code = 'http_error') {}
}

require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/PhoneNumber.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Messaging/SmsResult.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Messaging/SmsProvider.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Messaging/OtpSmsProvider.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Messaging/MeliPayamakRestProvider.php';
require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Messaging/SmsProviderFactory.php';

use Bamero\Core\Messaging\MeliPayamakRestProvider;
use Bamero\Core\Messaging\OtpSmsProvider;
use Bamero\Core\Messaging\SmsProviderFactory;

function assert_true(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

putenv('BAMERO_MELIPAYAMAK_USERNAME=account-user');
putenv('BAMERO_MELIPAYAMAK_API_KEY=api-key-secret');
putenv('BAMERO_MELIPAYAMAK_SENDER=50001234');
assert_true(SmsProviderFactory::isConfigured(), 'detects complete environment configuration');
assert_true(SmsProviderFactory::make() instanceof MeliPayamakRestProvider, 'builds configured MeliPayamak adapter');
putenv('BAMERO_MELIPAYAMAK_API_KEY=');
assert_true(!SmsProviderFactory::isConfigured(), 'does not report partial configuration as ready');
putenv('BAMERO_MELIPAYAMAK_API_KEY=api-key-secret');

$provider = new MeliPayamakRestProvider('account-user', 'api-key-secret', '50001234');
assert_true($provider instanceof OtpSmsProvider, 'supports provider-native OTP endpoint');

$responseQueue[] = [
    'response' => ['code' => 200],
    'body' => json_encode(['RetStatus' => 1, 'Value' => '987654321'], JSON_THROW_ON_ERROR),
];
$result = $provider->send('۰۹۱۲۳۴۵۶۷۸۹', 'کد آزمایشی');
assert_true($result->isSuccess(), 'accepts successful MeliPayamak response');
assert_true($result->messageId === '987654321', 'returns provider message id');
assert_true($lastRequest[0] === 'https://rest.payamak-panel.com/api/SendSMS/SendSMS', 'uses official HTTPS REST endpoint');
assert_true($lastRequest[1]['sslverify'] === true, 'TLS certificate verification stays enabled');
assert_true($lastRequest[1]['timeout'] <= 20, 'request has a bounded timeout');
assert_true($lastRequest[1]['body']['password'] === 'api-key-secret', 'uses API key in provider password field');
assert_true($lastRequest[1]['body']['to'] === '09123456789', 'sends normalized Iranian local mobile number');

$responseQueue[] = [
    'response' => ['code' => 200],
    'body' => '987654322',
];
$otp = $provider->sendOtpCode('09123456789', '123456');
assert_true($otp->isSuccess(), 'accepts successful native OTP response');
assert_true($lastRequest[0] === 'https://rest.payamak-panel.com/api/SendSMS/SendOtp', 'uses provider-native SendOtp endpoint');
assert_true($lastRequest[1]['body']['code'] === '123456', 'sends OTP code using native OTP parameter');
assert_true($lastRequest[1]['sslverify'] === true, 'keeps TLS verification enabled for OTP endpoint');

$beforeOtpInvalid = $lastRequest;
$invalidOtp = $provider->sendOtpCode('09123456789', '12AB56');
assert_true(!$invalidOtp->isSuccess(), 'rejects malformed OTP code');
assert_true($lastRequest === $beforeOtpInvalid, 'does not call provider for malformed OTP code');

$responseQueue[] = [
    'response' => ['code' => 200],
    'body' => json_encode(['RetStatus' => 0, 'Value' => '-110'], JSON_THROW_ON_ERROR),
];
$failed = $provider->send('09123456789', 'test');
assert_true(!$failed->isSuccess(), 'rejects provider-level error codes returned with HTTP 200');
assert_true($failed->errorCode === '-110', 'exposes provider error code without response body');

$before = $lastRequest;
$invalid = $provider->send('02112345678', 'test');
assert_true(!$invalid->isSuccess(), 'rejects landline recipients');
assert_true($lastRequest === $before, 'does not call provider for invalid recipient');

$empty = $provider->send('09123456789', "  ");
assert_true(!$empty->isSuccess(), 'rejects empty message');

$responseQueue[] = new WP_Error('network_failure');
$network = $provider->send('09123456789', 'test');
assert_true(!$network->isSuccess(), 'converts transport errors into safe failure result');
assert_true($network->messageId === null, 'never returns a message id on transport failure');

echo "MeliPayamak provider contract tests passed.\n";
