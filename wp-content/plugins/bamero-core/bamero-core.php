<?php
/**
 * Plugin Name: Bamero Core
 * Description: هستهٔ مستقل قابلیت‌های اختصاصی فروشگاه Bamero.
 * Version: 0.7.0
 * Requires PHP: 8.5
 * Requires Plugins: woocommerce
 * Text Domain: bamero-core
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BAMERO_CORE_VERSION', '0.7.0');
define('BAMERO_CORE_PATH', plugin_dir_path(__FILE__));

require_once BAMERO_CORE_PATH . 'src/Identity/PhoneNumber.php';
require_once BAMERO_CORE_PATH . 'src/Identity/OtpPurpose.php';
require_once BAMERO_CORE_PATH . 'src/Identity/OtpCode.php';
require_once BAMERO_CORE_PATH . 'src/Identity/OtpChallengeStore.php';
require_once BAMERO_CORE_PATH . 'src/Messaging/SmsResult.php';
require_once BAMERO_CORE_PATH . 'src/Messaging/SmsProvider.php';
require_once BAMERO_CORE_PATH . 'src/Messaging/OtpSmsProvider.php';
require_once BAMERO_CORE_PATH . 'src/Messaging/MeliPayamakRestProvider.php';
require_once BAMERO_CORE_PATH . 'src/Messaging/SmsProviderFactory.php';
require_once BAMERO_CORE_PATH . 'src/Admin/SmsAdminPage.php';
require_once BAMERO_CORE_PATH . 'src/Catalog/ProductCatalog.php';
require_once BAMERO_CORE_PATH . 'src/Catalog/WooCommerceCatalogImporter.php';
require_once BAMERO_CORE_PATH . 'src/Catalog/DemoDataSeeder.php';
require_once BAMERO_CORE_PATH . 'src/Database/Schema.php';

add_action('admin_menu', [\Bamero\Core\Admin\SmsAdminPage::class, 'register']);
add_action('init', [\Bamero\Core\Catalog\WooCommerceCatalogImporter::class, 'registerCli'], 20);
add_action('init', [\Bamero\Core\Catalog\DemoDataSeeder::class, 'registerCli'], 21);
add_action('plugins_loaded', [\Bamero\Core\Database\Schema::class, 'maybeMigrate'], 20);
register_activation_hook(__FILE__, [\Bamero\Core\Database\Schema::class, 'migrate']);
