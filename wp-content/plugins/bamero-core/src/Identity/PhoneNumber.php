<?php
declare(strict_types=1);

namespace Bamero\Core\Identity;

/**
 * Normalizes Iranian mobile numbers to E.164 (+989xxxxxxxxx).
 *
 * This class deliberately validates Iranian mobile numbers only. It does not
 * infer or accept landlines and foreign numbers as customer identities.
 */
final class PhoneNumber
{
    /**
     * Return a canonical Iranian mobile number, or null when invalid.
     */
    public static function normalize(string $input): ?string
    {
        $input = trim($input);

        if ($input === '' || preg_match('/[^0-9۰-۹٠-٩\s()+.\-]/u', $input) === 1) {
            return null;
        }

        $digits = strtr($input, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);

        $digits = preg_replace('/[^0-9]/', '', $digits);

        if (!is_string($digits) || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0098')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '98')) {
            // Already has Iran's country calling code.
        } elseif (str_starts_with($digits, '0')) {
            $digits = '98' . substr($digits, 1);
        } elseif (str_starts_with($digits, '9')) {
            $digits = '98' . $digits;
        } else {
            return null;
        }

        if (preg_match('/\A989[0-9]{9}\z/', $digits) !== 1) {
            return null;
        }

        return '+' . $digits;
    }
}
