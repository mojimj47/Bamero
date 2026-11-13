<?php
declare(strict_types=1);

namespace Bamero\Core\Catalog;

final class DemoDataSeeder
{
    private const CUSTOMER_COUNT = 5;

    /** Fictitious Toman values for local/staging UI testing only. */
    private const DEMO_PRICES = [
        'BAM-PM-BT7' => 1850000, 'BAM-PM-BT5' => 1450000, 'BAM-PM-BT2' => 980000,
        'BAM-PM-PM20' => 1250000, 'BAM-PM-PM30' => 2150000, 'BAM-PM-SP4' => 320000,
        'BAM-PM-6000' => 890000, 'BAM-PM-710' => 1650000, 'BAM-PM-770' => 2350000,
        'BAM-PM-760' => 2850000, 'BAM-NIP-1348' => 1980000, 'BAM-NIP-830' => 3250000,
        'BAM-NIP-751-BROWN' => 485000, 'BAM-NIP-SPRAY-43' => 395000,
        'BAM-CAS-TILE-100' => 420000, 'BAM-CAS-TILE-255' => 590000,
        'BAM-CAS-TILE-295' => 720000, 'BAM-CAS-PU-CBTR10' => 980000,
        'BAM-CAS-WOOD-GLUE' => 365000, 'BAM-CAS-SIL-AQUARIUM' => 275000,
        'BAM-CAS-FAST-ADHESIVE' => 185000, 'BAM-CAS-PVC-ADHESIVE' => 240000,
        'BAM-CAS-EPOXY-QUICK' => 315000,
    ];

    public static function seed(): array
    {
        self::assertSafeEnvironment();
        if (!class_exists('WooCommerce') || !function_exists('wc_get_product_id_by_sku')) {
            throw new \RuntimeException('WooCommerce must be active before seeding demo data.');
        }

        $catalog = WooCommerceCatalogImporter::import();
        $priced = 0;
        foreach (self::DEMO_PRICES as $sku => $price) {
            $productId = wc_get_product_id_by_sku($sku);
            $product = $productId ? wc_get_product($productId) : false;
            if (!$product || $product->get_meta('_bamero_catalog_fixture_created') !== '1') {
                continue;
            }
            $product->set_status('draft');
            $product->set_regular_price((string) $price);
            $product->set_price((string) $price);
            $product->set_manage_stock(true);
            $product->set_stock_quantity(7);
            $product->set_stock_status('instock');
            $product->update_meta_data('_bamero_demo_price', '1');
            $product->update_meta_data('_bamero_demo_price_currency', 'IRT');
            $product->update_meta_data('_bamero_demo_price_note', 'قیمت کاملاً آزمایشی؛ قیمت واقعی فروش نیست.');
            $product->update_meta_data('_bamero_demo_stock', '1');
            $product->save();
            $priced++;
        }

        $usersCreated = 0;
        for ($i = 1; $i <= self::CUSTOMER_COUNT; $i++) {
            $login = sprintf('bamero_demo_customer_%02d', $i);
            if (username_exists($login)) {
                continue;
            }
            $email = sprintf('customer%02d@demo.example.invalid', $i);
            if (email_exists($email)) {
                continue;
            }
            $userId = wp_insert_user([
                'user_login' => $login,
                'user_pass' => bin2hex(random_bytes(32)),
                'user_email' => $email,
                'first_name' => 'کاربر',
                'last_name' => sprintf('آزمایشی %02d', $i),
                'display_name' => sprintf('کاربر آزمایشی %02d', $i),
                'role' => 'customer',
            ]);
            if (is_wp_error($userId)) {
                throw new \RuntimeException('Unable to create demo customer: ' . $userId->get_error_message());
            }
            update_user_meta($userId, '_bamero_demo_fixture', '1');
            update_user_meta($userId, '_bamero_demo_notice', 'حساب ساختگی برای آزمون؛ مشتری واقعی نیست.');
            $usersCreated++;
        }

        return [
            'catalog_created' => $catalog['created'],
            'catalog_updated' => $catalog['updated'],
            'catalog_skipped' => $catalog['skipped'],
            'demo_prices_set' => $priced,
            'demo_customers_created' => $usersCreated,
        ];
    }

    public static function registerCli(): void
    {
        if (defined('WP_CLI') && WP_CLI && class_exists('WP_CLI')) {
            \WP_CLI::add_command('bamero demo seed', static function (): void {
                try {
                    $result = self::seed();
                    \WP_CLI::success(wp_json_encode($result, JSON_UNESCAPED_UNICODE));
                } catch (\Throwable $error) {
                    \WP_CLI::error($error->getMessage());
                }
            });
        }
    }

    private static function assertSafeEnvironment(): void
    {
        $environment = function_exists('wp_get_environment_type') ? wp_get_environment_type() : 'production';
        $allowed = defined('BAMERO_ALLOW_DEMO_DATA') && BAMERO_ALLOW_DEMO_DATA === true;
        if (!$allowed || !in_array($environment, ['local', 'development', 'staging'], true)) {
            throw new \RuntimeException(
                'Demo seeding blocked: set WP_ENVIRONMENT_TYPE to local/development/staging and BAMERO_ALLOW_DEMO_DATA=true. Production is never allowed.'
            );
        }
    }
}
