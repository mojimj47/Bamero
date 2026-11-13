<?php
/**
 * Plugin Name: Bamero Core
 * Description: هستهٔ مستقل قابلیت‌های اختصاصی فروشگاه Bamero.
 * Version: 0.1.0
 * Requires PHP: 8.5
 * Requires Plugins: woocommerce
 * Text Domain: bamero-core
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BAMERO_CORE_VERSION', '0.1.0');
define('BAMERO_CORE_PATH', plugin_dir_path(__FILE__));

require_once BAMERO_CORE_PATH . 'src/Identity/PhoneNumber.php';
