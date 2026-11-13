<?php
declare(strict_types=1);

namespace Bamero\Core\Database;

final class Schema
{
    public const VERSION = '1';

    public static function maybeMigrate(): void
    {
        if (get_option('bamero_db_version') !== self::VERSION) {
            self::migrate();
        }
    }

    public static function migrate(): void
    {
        global $wpdb;
        $path = BAMERO_CORE_PATH . 'database/schema.sql';
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException('Bamero database schema file is unavailable.');
        }

        $sql = str_replace('{{prefix}}', $wpdb->prefix, $contents);
        $statements = preg_split('/;\s*(?:\r?\n|$)/', trim($sql));
        foreach ($statements ?: [] as $statement) {
            $statement = trim($statement);
            if ($statement === '') {
                continue;
            }
            if ($wpdb->query($statement) === false) {
                throw new \RuntimeException('Bamero database migration failed: ' . (string) $wpdb->last_error);
            }
        }
        update_option('bamero_db_version', self::VERSION, false);
    }
}
