<?php
declare(strict_types=1);
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'bamero_test';
$user = getenv('DB_USER') ?: 'bamero';
$password = getenv('DB_PASSWORD') ?: 'bamero_test_password';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
$pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]);
$sql = file_get_contents(__DIR__ . '/../wp-content/plugins/bamero-core/database/schema.sql');
if ($sql === false) { throw new RuntimeException('Unable to read production schema migration.'); }
$sql = str_replace('{{prefix}}', 'integration_', $sql);
$statements = preg_split('/;\s*(?:\r?\n|$)/', trim($sql)) ?: [];
foreach ($statements as $statement) { if (trim($statement) !== '') { $pdo->exec(trim($statement)); } }
foreach ($statements as $statement) { if (trim($statement) !== '') { $pdo->exec(trim($statement)); } }
$tables = $pdo->query("SHOW TABLES LIKE 'integration_bamero_%'")->fetchAll(PDO::FETCH_COLUMN);
if (count($tables) !== 2) { throw new RuntimeException('Expected OTP challenge and rate-limit tables to exist.'); }
$challenge = $pdo->query("SHOW CREATE TABLE integration_bamero_otp_challenges")->fetch(PDO::FETCH_ASSOC);
if (!$challenge || !str_contains($challenge['Create Table'], 'idx_phone_purpose_created') || !str_contains($challenge['Create Table'], 'idx_expiry_consumed') || !str_contains($challenge['Create Table'], 'InnoDB')) { throw new RuntimeException('OTP table is missing required InnoDB indexes.'); }
$rate = $pdo->query("SHOW CREATE TABLE integration_bamero_rate_limits")->fetch(PDO::FETCH_ASSOC);
$rateDdl = preg_replace('/[\x60\s]+/', '', (string) ($rate['Create Table'] ?? ''));
if (!$rate || !str_contains($rateDdl, 'PRIMARYKEY(bucket_key)') || !str_contains($rate['Create Table'], 'InnoDB')) { throw new RuntimeException('Rate-limit table is missing its atomic bucket key.'); }
echo "MariaDB schema integration tests passed against a real database.\n";
