<?php
declare(strict_types=1);

$seeder = file_get_contents(__DIR__ . '/../wp-content/plugins/bamero-core/src/Catalog/DemoDataSeeder.php');
if ($seeder === false) {
    throw new RuntimeException('Demo seeder source is missing.');
}
$required = [
    "WP_ENVIRONMENT_TYPE",
    "BAMERO_ALLOW_DEMO_DATA",
    "example.invalid",
    "_bamero_demo_fixture",
    "_bamero_demo_price",
    "set_status('draft')",
    "random_bytes(32)",
];
foreach ($required as $needle) {
    if (!str_contains($seeder, $needle)) {
        throw new RuntimeException('Demo seeder is missing required safety marker: ' . $needle);
    }
}
if (str_contains($seeder, 'wp_insert_post') || str_contains($seeder, 'wc_create_order')) {
    throw new RuntimeException('Demo seeder must not create orders or bypass WooCommerce product CRUD.');
}
if (substr_count($seeder, "=> ") < 20) {
    throw new RuntimeException('Expected a populated demo price map.');
}
echo "Demo data safety contract tests passed.\n";
