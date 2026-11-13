#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

RETENTION_DAYS="${RETENTION_DAYS:-14}"
BACKUP_DIR="${BACKUP_DIR:-./backups}"
mkdir -p "$BACKUP_DIR"

timestamp="$(date -u +%Y%m%dT%H%M%SZ)"
output="$BACKUP_DIR/bamero-mariadb-$timestamp.sql.gz"

docker compose exec -T db sh -ec '
  exec mariadb-dump \
    --user="$MARIADB_USER" \
    --password="$MARIADB_PASSWORD" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    --events \
    "$MARIADB_DATABASE"
' | gzip -c > "$output"

test -s "$output"
gzip -t "$output"
find "$BACKUP_DIR" -type f -name 'bamero-mariadb-*.sql.gz' -mtime +"$RETENTION_DAYS" -delete
printf 'Database backup created and verified: %s\n' "$output"
