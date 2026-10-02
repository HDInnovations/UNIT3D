#!/usr/bin/env bash
# Builds and starts the isolated `capacity` stack, then migrates and seeds
# deterministic fixtures. Safe to re-run (idempotent): skips seeding if
# fixtures already exist unless --fresh is passed.
#
# Usage: scripts/capacity/bootstrap.sh [--fresh] [--torrents=N] [--users=N] [--peers=N]
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

COMPOSE=(docker compose -p capacity -f docker-compose.capacity.yml)
FRESH=0
BUILD=0
SEED_ARGS=()

for arg in "$@"; do
    case "$arg" in
        --fresh) FRESH=1 ;;
        --build) BUILD=1 ;;
        --torrents=*|--users=*|--peers=*) SEED_ARGS+=("$arg") ;;
        *) echo "Unknown argument: $arg" >&2; exit 1 ;;
    esac
done

if [ ! -f .env.capacity ]; then
    echo "==> Creating .env.capacity from .env.capacity.example"
    cp .env.capacity.example .env.capacity
fi

# Generate APP_KEY on the HOST and write it into .env.capacity directly,
# BEFORE anything starts: capacity-app bind-mounts the repo read-only, so
# `artisan key:generate` run *inside* that container has nowhere writable to
# put it, and the key must exist in the env file before containers read it
# at startup anyway.
if ! grep -q '^APP_KEY=base64:' .env.capacity 2>/dev/null; then
    echo "==> Generating APP_KEY on the host"
    NEW_KEY="base64:$(openssl rand -base64 32)"
    if grep -q '^APP_KEY=' .env.capacity; then
        sed -i "s#^APP_KEY=.*#APP_KEY=${NEW_KEY}#" .env.capacity
    else
        echo "APP_KEY=${NEW_KEY}" >> .env.capacity
    fi
fi

if [ "$BUILD" = "1" ] || ! docker image inspect sail-8.4/fpm >/dev/null 2>&1; then
    echo "==> Building capacity-app runtime image"
    "${COMPOSE[@]}" build capacity-app
else
    echo "==> Reusing the verified local runtime image (pass --build after runtime changes)"
fi

# Stop old producers as well as consumers: timed-out HTTP requests can still run.
"${COMPOSE[@]}" stop capacity-nginx capacity-app capacity-queue-default capacity-queue-tracker capacity-schedule

echo "==> Starting isolated dependency containers (mysql/redis/meilisearch)"
"${COMPOSE[@]}" up -d capacity-mysql capacity-redis capacity-meilisearch

echo "==> Waiting for dependencies to report healthy"
for svc in capacity-mysql capacity-redis capacity-meilisearch; do
    for _ in $(seq 1 60); do
        status=$("${COMPOSE[@]}" ps --format '{{.Health}}' "$svc" 2>/dev/null || echo "")
        [ "$status" = "healthy" ] && break
        sleep 1
    done
done

# Named volumes (capacity_storage/capacity_bootstrap_cache/capacity_config_cache)
# start out empty and root-owned; the app container's entrypoint runs
# `gosu sail` for any command, so storage/bootstrap-cache/the config-cache
# tmpdir must be writable by that uid BEFORE the first real request/artisan
# call, not discovered via a permission-denied error.
echo "==> Initializing storage/bootstrap-cache/config-cache volume ownership"
"${COMPOSE[@]}" run --rm --user root --entrypoint sh capacity-app -c '
    mkdir -p /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache /var/www/html/storage/framework/testing
    mkdir -p /var/www/html/storage/logs
    mkdir -p /var/www/html/bootstrap/cache
    mkdir -p /tmp/capacity-views
    chown -R "${WWWUSER:-1000}":sail /var/www/html/storage /var/www/html/bootstrap/cache /var/www/capacity-results /tmp
'

echo "==> Starting isolated PHP-FPM for setup commands (ingress stays stopped)"
"${COMPOSE[@]}" up -d capacity-app

echo "==> Waiting for capacity-app to be able to run artisan"
for _ in $(seq 1 60); do
    if "${COMPOSE[@]}" exec -T -u sail capacity-app php -v >/dev/null 2>&1; then
        break
    fi
    sleep 1
done

# Any artisan run with overridden env (e.g. the test suite) may leave a config
# cache behind in the shared /tmp volume; drop it so the benchmark always boots
# the real `capacity` environment.
"${COMPOSE[@]}" exec -T -u sail capacity-app rm -f /tmp/capacity-config.php /tmp/capacity-routes.php /tmp/capacity-events.php /tmp/capacity-services.php

echo "==> Creating the isolated 'testing' database (for running the real test suite ONLY against this isolated stack, never the live one)"
"${COMPOSE[@]}" exec -T capacity-mysql mysql -uroot -pcapacity_root_local_only -e "
    CREATE DATABASE IF NOT EXISTS testing;
    GRANT ALL PRIVILEGES ON testing.* TO 'capacity'@'%';
    FLUSH PRIVILEGES;
"

HAS_FIXTURES=$("${COMPOSE[@]}" exec -T -u sail capacity-app php -r '
    require "/var/www/html/vendor/autoload.php";
    $app = require "/var/www/html/bootstrap/app.php";
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo Illuminate\Support\Facades\Schema::hasTable("users")
        && Illuminate\Support\Facades\DB::table("users")->where("username", "capacity_canary_user")->exists() ? "1" : "0";
')

if [ "$FRESH" = "1" ] || [ "$HAS_FIXTURES" != "1" ]; then
    # The later upload-kind migration inserts lookup rows. Seed the schema's
    # baseline first so those rows cannot occupy TypeSeeder's fixed IDs.
    # This destroys only the explicitly disposable, isolated capacity DB.
    echo "==> Preparing isolated baseline schema and lookups"
    "${COMPOSE[@]}" exec -T capacity-redis redis-cli FLUSHALL
    "${COMPOSE[@]}" exec -T -u sail capacity-app php artisan migrate:fresh --force --path=database/migrations/2026_09_29_014701_add_music_and_book_metadata.php
    "${COMPOSE[@]}" exec -T -u sail capacity-app php artisan db:seed --force
    "${COMPOSE[@]}" exec -T -u sail capacity-app php artisan migrate --force

    echo "==> Seeding deterministic capacity fixtures"
    "${COMPOSE[@]}" exec -T -u sail capacity-app php artisan capacity:seed-fixtures "${SEED_ARGS[@]}" | tee scripts/capacity/results/seed-fixtures.log

    echo "==> Syncing torrents to the isolated Meilisearch index (and waiting for indexing to finish)"
    "${COMPOSE[@]}" exec -T -u sail capacity-app php artisan scout:sync-index-settings
    "${COMPOSE[@]}" exec -T -u sail capacity-app php artisan auto:sync_torrents_to_meilisearch --wipe

    echo "==> Waiting for the Meilisearch index to finish processing all enqueued tasks"
    for _ in $(seq 1 120); do
        pending=$("${COMPOSE[@]}" exec -T -u sail capacity-app php -r '
            require "/var/www/html/vendor/autoload.php";
            $client = new Meilisearch\Client(getenv("MEILISEARCH_HOST"), getenv("MEILISEARCH_KEY"));
            $tasks = $client->getTasks((new Meilisearch\Contracts\TasksQuery())->setStatuses(["enqueued", "processing"]));
            echo $tasks->getTotal();
        ' 2>/dev/null || echo "")
        if [ "${pending:-1}" = "0" ]; then
            break
        fi
        sleep 1
    done
    if [ "${pending:-1}" != "0" ]; then
        echo "Isolated Meilisearch indexing did not become ready" >&2
        exit 1
    fi
else
    echo "==> Fixtures already present; skipping seed (pass --fresh to reseed)"
fi

echo "==> Starting isolated queue consumers and scheduler"
"${COMPOSE[@]}" up -d capacity-queue-default capacity-queue-tracker capacity-schedule capacity-nginx

echo "==> Bootstrap complete. Run scripts/capacity/run-scenario.sh <scenario> next."
