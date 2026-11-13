<?php
declare(strict_types=1);

namespace Bamero\Core\Catalog;

final class WooCommerceCatalogImporter
{
    /**
     * @return array{created:int,updated:int,skipped:int}
     */
    public static function import(): array
    {
        if (!function_exists('wc_get_product_id_by_sku') || !class_exists('WC_Product_Simple')) {
            throw new \RuntimeException('WooCommerce must be active before catalog import.');
        }

        self::registerBrandTaxonomy();
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach (ProductCatalog::all() as $record) {
            if (!self::isValidRecord($record)) {
                $skipped++;
                continue;
            }

            $productId = wc_get_product_id_by_sku($record['sku']);
            $isNew = !$productId;
            $product = $isNew ? new \WC_Product_Simple() : wc_get_product($productId);
            if (!$product) {
                $skipped++;
                continue;
            }

            $product->set_name($record['name']);
            $product->set_sku($record['sku']);
            $product->set_description($record['description']);
            $product->set_short_description($record['description']);
            $product->set_status($isNew ? 'draft' : $product->get_status());
            if ($isNew) {
                $product->set_virtual(false);
                $product->set_downloadable(false);
                // New catalog entries start with WooCommerce's default unmanaged stock.
                // Never change stock-management settings on an existing merchant product.
                $product->set_manage_stock(false);
                $product->set_category_ids([self::categoryId($record['category'])]);
                $product->update_meta_data('_bamero_catalog_fixture_created', '1');
            }
            $product->update_meta_data('_bamero_product_code', $record['product_code']);
            $product->update_meta_data('_bamero_packaging_note', $record['packaging']);
            $product->update_meta_data('_bamero_catalog_source_url', esc_url_raw($record['source_url']));
            $product->update_meta_data('_bamero_catalog_source_checked_at', '2026-11-14');
            $product->update_meta_data('_bamero_catalog_version', ProductCatalog::VERSION);

            $productId = $product->save();
            if ($isNew) {
                self::assignBrand((int) $productId, $record['brand']);
                $created++;
            } else {
                $updated++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped];
    }

    public static function registerCli(): void
    {
        if (defined('WP_CLI') && WP_CLI && class_exists('WP_CLI')) {
            \WP_CLI::add_command('bamero catalog import', static function (): void {
                try {
                    $result = self::import();
                    \WP_CLI::success(sprintf(
                        'Catalog import finished: %d created, %d updated, %d skipped. New products are drafts.',
                        $result['created'],
                        $result['updated'],
                        $result['skipped']
                    ));
                } catch (\Throwable $error) {
                    \WP_CLI::error($error->getMessage());
                }
            });
        }
    }

    private static function isValidRecord(array $record): bool
    {
        foreach (['name', 'brand', 'sku', 'category', 'description', 'source_url'] as $field) {
            if (!isset($record[$field]) || trim($record[$field]) === '') {
                return false;
            }
        }
        $url = parse_url($record['source_url']);
        return is_array($url) && ($url['scheme'] ?? '') === 'https' && !empty($url['host']);
    }

    private static function categoryId(string $name): int
    {
        $term = term_exists($name, 'product_cat');
        if (!$term) {
            $term = wp_insert_term($name, 'product_cat');
        }
        if (is_wp_error($term)) {
            throw new \RuntimeException('Unable to create product category: ' . $name);
        }
        return (int) (is_array($term) ? $term['term_id'] : $term);
    }

    private static function registerBrandTaxonomy(): void
    {
        if (!taxonomy_exists('product_brand')) {
            register_taxonomy('product_brand', ['product'], [
                'label' => 'برند محصولات',
                'public' => true,
                'hierarchical' => false,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'rewrite' => ['slug' => 'brand'],
            ]);
        }
    }

    private static function assignBrand(int $productId, string $brand): void
    {
        $term = term_exists($brand, 'product_brand');
        if (!$term) {
            $term = wp_insert_term($brand, 'product_brand');
        }
        if (is_wp_error($term)) {
            throw new \RuntimeException('Unable to create product brand: ' . $brand);
        }
        $termId = (int) (is_array($term) ? $term['term_id'] : $term);
        wp_set_object_terms($productId, [$termId], 'product_brand', false);
    }
}
