<?php
declare(strict_types=1);

require_once __DIR__ . '/../wp-content/plugins/bamero-core/src/Catalog/ProductCatalog.php';

use Bamero\Core\Catalog\ProductCatalog;

$records = ProductCatalog::all();
$skus = [];
$brands = [];
$required = ['name', 'brand', 'sku', 'category', 'product_code', 'packaging', 'description', 'source_url'];

foreach ($records as $index => $record) {
    foreach ($required as $field) {
        if (!isset($record[$field]) || trim($record[$field]) === '') {
            throw new RuntimeException("Catalog record {$index} is missing {$field}");
        }
    }
    if (isset($skus[$record['sku']])) {
        throw new RuntimeException('Duplicate SKU: ' . $record['sku']);
    }
    $url = parse_url($record['source_url']);
    if (!is_array($url) || ($url['scheme'] ?? '') !== 'https' || empty($url['host'])) {
        throw new RuntimeException('Invalid HTTPS source URL for SKU: ' . $record['sku']);
    }
    $skus[$record['sku']] = true;
    $brands[$record['brand']] = true;
}

if (count($records) < 15 || !isset($brands['پارس مهر'], $brands['نیپون'], $brands['کاسپین'])) {
    throw new RuntimeException('Expected Pars Mehr, Nippon and Caspian catalog entries.');
}

// Guard the importer contract: stock-management settings may only be initialized
// for new products, never overwritten on existing WooCommerce products.
$importer = file_get_contents(__DIR__ . '/../wp-content/plugins/bamero-core/src/Catalog/WooCommerceCatalogImporter.php');
if ($importer === false || substr_count($importer, 'set_manage_stock(false)') !== 1) {
    throw new RuntimeException('Catalog importer must initialize stock management only once for new products.');
}
$newProductBlock = strpos($importer, 'if ($isNew) {');
$stockSetting = strpos($importer, '$product->set_manage_stock(false);');
if ($newProductBlock === false || $stockSetting === false || $stockSetting < $newProductBlock) {
    throw new RuntimeException('Existing product stock configuration must remain untouched.');
}

echo sprintf("Catalog manifest tests passed (%d records, %d brands).\n", count($records), count($brands));
