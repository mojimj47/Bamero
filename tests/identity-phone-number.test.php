<?php
declare(strict_types=1);

require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Identity/PhoneNumber.php';

use Bamero\Core\Identity\PhoneNumber;

$cases = [
    ['09123456789', '+989123456789'],
    ['۰۹۱۲۳۴۵۶۷۸۹', '+989123456789'],
    ['٠٩١٢٣٤٥٦٧٨٩', '+989123456789'],
    ['+989123456789', '+989123456789'],
    ['00989123456789', '+989123456789'],
    ['98 912 345 6789', '+989123456789'],
    ['9123456789', '+989123456789'],
    ['0912-345-6789', '+989123456789'],
];

foreach ($cases as [$input, $expected]) {
    $actual = PhoneNumber::normalize($input);
    if ($actual !== $expected) {
        fwrite(STDERR, sprintf("FAIL: %s => %s (expected %s)\n", $input, var_export($actual, true), $expected));
        exit(1);
    }
}

$invalid = ['', '123', '02112345678', '+12025550123', '0912345678', '091234567890', 'phone09123456789'];
foreach ($invalid as $input) {
    if (PhoneNumber::normalize($input) !== null) {
        fwrite(STDERR, sprintf("FAIL: invalid number accepted: %s\n", $input));
        exit(1);
    }
}

echo "Identity phone normalization tests passed.\n";
