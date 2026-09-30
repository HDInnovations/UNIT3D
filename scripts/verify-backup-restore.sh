#!/usr/bin/env bash
set -euo pipefail

project_root=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)
readonly project_root
readonly restore_database='unit3d_restore_check'
readonly restore_sql="$project_root/storage/app/restore-check.sql"

cleanup() {
    docker compose exec -T mysql sh -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS unit3d_restore_check"' >/dev/null
    rm -f "$restore_sql"
}

cd "$project_root"
trap cleanup EXIT

docker compose exec -T laravel.test php artisan tinker --execute='
$disk = Storage::disk("backups");
$found = false;
foreach (collect($disk->allFiles())->filter(fn (string $path): bool => str_ends_with($path, ".zip"))->sortDesc() as $path) {
    $archive = new ZipArchive;
    if ($archive->open($disk->path($path)) !== true) {
        continue;
    }

    $archive->setPassword((string) config("backup.backup.password"));
    for ($index = 0; $index < $archive->numFiles; $index++) {
        $name = (string) $archive->getNameIndex($index);
        if (!str_starts_with($name, "db-dumps/")) {
            continue;
        }

        $stream = $archive->getStream($name);
        if ($stream === false) {
            throw new RuntimeException("Cannot read database dump from {$path}.");
        }

        file_put_contents(storage_path("app/restore-check.sql"), stream_get_contents($stream));
        fclose($stream);
        $archive->close();
        $found = true;
        break 2;
    }

    $archive->close();
}

if (!$found) {
    throw new RuntimeException("No encrypted database backup was found.");
}
'

docker compose exec -T mysql sh -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE unit3d_restore_check"'
docker compose exec -T mysql sh -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" unit3d_restore_check' < "$restore_sql"
table_count=$(docker compose exec -T mysql sh -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '\''unit3d_restore_check'\''"')

if [[ "$table_count" -eq 0 ]]; then
    echo 'Restore produced no tables.' >&2
    exit 1
fi

echo "Restored database backup into $restore_database with $table_count tables."
