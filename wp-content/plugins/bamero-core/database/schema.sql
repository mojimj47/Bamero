CREATE TABLE IF NOT EXISTS {{prefix}}bamero_otp_challenges (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  phone_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  purpose VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  code_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  expires_at DATETIME NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  consumed_at DATETIME NULL DEFAULT NULL,
  ip_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL,
  provider_message_id VARCHAR(100) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_phone_purpose_created (phone_hash, purpose, created_at),
  KEY idx_expiry_consumed (expires_at, consumed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS {{prefix}}bamero_rate_limits (
  bucket_key CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  hit_count INT UNSIGNED NOT NULL DEFAULT 0,
  window_started_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (bucket_key),
  KEY idx_rate_window (window_started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
