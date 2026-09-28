# Local production-like setup

This configuration is intentionally private to the workstation. Tracker ingress is `https://tracker.iamanro.dev`, terminated by Caddy at `10.0.0.4` and restricted there to `10.0.0.0/24` and Tailscale `100.64.0.0/10`. nginx exposes a private TLS upstream only on `10.0.0.147:8444`; application, database, Redis, Meilisearch, and SMTP ports are not host-published. ARR and Mailpit interfaces remain loopback-only.

## First boot after a new database volume

```sh
docker compose -f docker-compose.yml -f docker-compose.arr.yml up -d
docker compose exec -T laravel.test php artisan migrate --force
docker compose exec -T laravel.test php artisan db:seed --force
docker compose exec -T laravel.test php artisan scout:sync-index-settings
docker compose exec -T laravel.test php artisan scout:import 'App\Models\Torrent'
```

The category seeder creates the private tracker catalog:

- Movies and TV — metadata-enabled; used by Radarr and Sonarr.
- Music and Games — metadata-enabled.
- Software, Books, and Other — generic releases without metadata.

Registration is invite-only (`config/other.php`). Members use a personal UNIT3D API token with the `/torznab/api` endpoint described in `ARR.md`.

## Local services

- Tracker: `https://localhost:8444`
- Mailpit dashboard: `http://localhost:8026`
- Prowlarr: `http://localhost:9696`
- Sonarr: `http://localhost:8989`
- Radarr: `http://localhost:7878`
- qBittorrent: `http://localhost:8085`

Mail uses the internal Mailpit SMTP service. Meilisearch uses a generated master key in `.env`; do not publish either service.

`TMDB_API_KEY` is configured locally for metadata. Never commit `.env`.

The internal `queue` worker fetches TMDb metadata and processes tracker jobs. The `schedule` worker applies peer, history, and announce batches every five seconds. Reverb serves private and presence WebSockets internally on port 8080; nginx proxies only `/app` and `/apps` over the tracker HTTPS origin. qBittorrent has the movie library mounted read-only at `/media/movies` so it can become the initial seed without changing source media.

MySQL runs from the `mysql:8.4` image on the `sail-mysql-8-4` volume. Keep the previous `sail-mysql` volume untouched until a separately verified backup-retention window ends; never attempt an in-place downgrade.

## Tests

PHPUnit uses the MySQL `testing` database created by Sail's initialization script. `php artisan test` may reset only that database; it must not be run with a production `DB_DATABASE` override.

## Dependency maintenance

Update dependencies in a working tree, then verify the resolved lockfiles and application:

```sh
docker compose exec -T -u "$(id -u):$(id -g)" laravel.test composer update --with-all-dependencies
npm update --ignore-scripts
docker compose exec -T -u "$(id -u):$(id -g)" laravel.test composer audit --locked
docker compose exec -T laravel.test php artisan test
npm run build
```

## Public deployment is a separate cutover

A real Internet deployment needs a public hostname, trusted TLS certificate, external SMTP sender, backups, and firewall rules. Replace only the nginx host bindings after those prerequisites are available; retain the internal-only database, Redis, Meilisearch, and adapter boundaries.
