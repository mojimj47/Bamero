<?php
declare(strict_types=1);

namespace Bamero\Core\Messaging;

final class SmsProviderFactory
{
    public static function isConfigured(): bool
    {
        return self::value('BAMERO_MELIPAYAMAK_USERNAME') !== ''
            && self::value('BAMERO_MELIPAYAMAK_API_KEY') !== ''
            && self::value('BAMERO_MELIPAYAMAK_SENDER') !== '';
    }

    public static function make(): ?SmsProvider
    {
        if (!self::isConfigured()) {
            return null;
        }

        return new MeliPayamakRestProvider(
            self::value('BAMERO_MELIPAYAMAK_USERNAME'),
            self::value('BAMERO_MELIPAYAMAK_API_KEY'),
            self::value('BAMERO_MELIPAYAMAK_SENDER')
        );
    }

    private static function value(string $name): string
    {
        $value = getenv($name);
        if (is_string($value) && $value !== '') {
            return trim($value);
        }

        if (defined($name)) {
            $constant = constant($name);
            return is_string($constant) ? trim($constant) : '';
        }

        return '';
    }
}
