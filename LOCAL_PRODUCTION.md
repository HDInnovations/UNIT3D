# Local production-like setup

This configuration is intentionally private to the workstation. Tracker ingress is `https://tracker.iamanro.dev`, terminated by Caddy at `10.0.0.4` and restricted there to `10.0.0.0/24` and Tailscale `100.64.0.0/10`. nginx exposes TLS only on the workstation Tailscale address at `:8444`; the PVE bridge at `10.0.0.10:8444` relays that upstream for Caddy. Application, database, Redis, Meilisearch, and SMTP ports are not host-published. ARR and Mailpit interfaces remain loopback-only.

## Cloudflare Tunnel

The connector runs in the dedicated unprivileged Debian 13 LXC `107` (`cloudflared`, `10.0.0.43`, 1 vCPU, 512 MiB RAM, 4 GiB disk) on PVE. It publishes no inbound port: `cloudflared` establishes only outbound connections to Cloudflare, which terminates public TLS for `tracker.iamanro.dev`.

`scripts/setup-cloudflare-tunnel.sh` provisions everything through the Cloudflare API: it creates or reuses the `vltava` tunnel, writes its ingress rules (`tracker.iamanro.dev` → `https://10.0.0.4:443`, host header `tracker.iamanro.dev`, `noTLSVerify` for that LAN-internal hop only), upserts the proxied `CNAME` to `<tunnel-id>.cfargotunnel.com`, then pipes the connector token over SSH stdin into LXC 107 and starts the service. The script is idempotent and waits for `https://tracker.iamanro.dev/login` to answer.

It needs an API token created at <https://dash.cloudflare.com/profile/api-tokens> with **Account → Cloudflare Tunnel → Edit**, **Account → Account Settings → Read**, and **Zone → DNS → Edit** on `iamanro.dev`. Pass it as `CLOUDFLARE_API_TOKEN`, or let the script prompt without echo. The connector token lives only in `/etc/cloudflared/tunnel.env` (mode `0600`), never in the repository or the tracker `.env`. The Caddy DNS-01 token is deliberately not reused: it holds zone permissions only and the tunnel API rejects it with error 10000.

This tracker intentionally does **not** use Cloudflare Access. Vltava itself gates the browser with invitation-only accounts; qBittorrent, RSS, API, and Torznab clients authenticate using their passkeys or personal tokens. Do not add an Access application or managed challenge to `tracker.iamanro.dev`, especially not to `/announce/*`, `/api/*`, `/rss/*`, `/torrent/download/*`, or `/torznab/api`.

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

### Freshness of tracker statistics

Clients announce every `ANNOUNCE_INTERVAL_MIN`–`ANNOUNCE_INTERVAL_MAX` seconds (300–360 s here; libtorrent clients never go below 300 s). An announce reaches the peer and history tables within one 5-second flush; the same flush recomputes seeders/leechers for the announced torrents and re-indexes them in Meilisearch (`App\Services\TorrentPeerCountSync`). `auto:sync_peers` still runs every five minutes as a full pass for peers that expire without announcing (ghost peers are marked inactive after two hours by `auto:flush_peers`). In the browser, the ratio bar polls every 10 s, the torrent page polls `/torrents/{id}/peers/counts` every 10 s, and the torrent list polls every 15 s while visible. Upstream caches that remain: statistics pages 10–30 min, home page blocks 5–10 min, RSS and the torrent API 5 min.

Timestamps are stored and processed in UTC (`app.timezone`); Blade renders every Carbon instance in `APP_DISPLAY_TIMEZONE` (Europe/Prague). Use `->toDisplayTimezone()` before `->format()` on a timestamp in a view. Date-only values (release dates, event days) are intentionally not converted. After changing the display handler, recompile views (`php artisan optimize`), because Blade inserts echo handlers at compile time.

Announces are buffered in the `announce` redis connection under `<cache prefix>:peers:batch`, `:histories:batch`, and `:announces:batch`, then flushed by the schedule worker. The prefix is derived from `APP_NAME`, and the long-running `queue` and `schedule` workers keep the configuration they booted with, so `docker compose restart queue schedule` is mandatory after changing `APP_NAME` or `CACHE_PREFIX`; otherwise the workers keep buffering under the previous prefix and no peer is ever recorded.

## Seeding from the MediaStack host

The MediaStack qBittorrent (PVE container 140, `10.0.0.40`) shares gluetun's network namespace. `tracker.iamanro.dev` now resolves to Cloudflare's public proxy addresses, so no `/etc/hosts` override or `10.0.0.0/24` firewall bypass is present. Public tracker requests follow gluetun's VPN default route; qBittorrent's `Session\Interface` is intentionally unbound and gluetun retains the leak-protection firewall.

The dedicated Cloudflare connector carries the original client address to Caddy. Caddy trusts forwarded headers only from `10.0.0.43/32` (LXC 107), reads the client address solely from `CF-Connecting-IP` (`client_ip_headers`), and replaces `X-Forwarded-For` with that single address (`header_up X-Forwarded-For {client_ip}`) before proxying. Laravel's `TrustProxies` trusts only `172.16.0.0/12` (nginx and its Docker gateway hop), so any earlier, client-supplied `X-Forwarded-For` entry is ignored and the tracker records the MediaStack VPN address rather than the connector's LAN address. Do not reset `$proxies` to `'*'`: that trusts every hop and lets clients spoof announce, ban, and rate-limit IPs.

The Caddy `tailscale_only` snippet explicitly rejects `10.0.0.43`, because the connector sits inside `10.0.0.0/24` while carrying public traffic; the tracker vhost uses `private_or_tunnel` instead. Caddy's DNS-01 token lives in `/etc/caddy/caddy.env` (mode `0600`) inside LXC 104, and the unit runs without `--environ` so the token is not written to the journal.

Tracker torrents are seeded with unlimited share limits so the global ratio and seeding-time limits do not stop them.

MySQL runs from the `mysql:8.4` image on the `sail-mysql-8-4` volume. Keep the previous `sail-mysql` volume untouched until a separately verified backup-retention window ends; never attempt an in-place downgrade.

## Backup, restore, and availability

- `vltava-backup.timer` runs daily at 03:15. It creates AES-encrypted database and filesystem archives using the existing Spatie backup configuration, retains them locally, and copies them to PVE at `/srv/storage/backup/proxmox/vltava`. The archive key is escrowed separately in a root-only PVE file, so an off-host restore remains possible after workstation loss.
- Run `scripts/verify-backup-restore.sh` after a backup or on a scheduled maintenance window. It restores the newest encrypted database dump into the disposable `unit3d_restore_check` database, checks that tables exist, then removes that database and the extracted SQL.
- PVE runs `vltava-healthcheck.timer` every five minutes. It requests `/login` twice from outside the tracker host: once through local DNS (Caddy → bridge → workstation) and once through public DNS over DoH (Cloudflare → tunnel LXC 107 → Caddy). On failure, `vltava-healthcheck-alert.service` mails root at most once per hour; that mail reaches `hello@iamanro.dev` only when the PVE notification target can deliver (see below). Investigate immediately if `systemctl status vltava-healthcheck` is failed.
- PVE notification mail is currently undeliverable: local DNS answers `iamanro.dev` with Caddy's `10.0.0.4` and no MX, so postfix queues everything with `connect to iamanro.dev[10.0.0.4]:25: Connection refused` (`mailq`). Configure an authenticated SMTP notification target (Datacenter → Notifications) before relying on these alerts.
- The PVE bridge `tracker-tailnet-bridge.service` runs socat as a systemd `DynamicUser` with a restricted sandbox; it needs no root privileges because `:8444` is unprivileged. The workstation's UFW Docker forwarding rules must allow its Tailscale source (`100.108.105.119`) to the container HTTPS port after Docker translates host port 8444: `sudo ufw route allow in on tailscale0 proto tcp from 100.108.105.119 to any port 443 comment 'PVE tracker TLS bridge'`. Without this persistent allowance, direct host requests succeed but Caddy returns 502 because PVE's connection is dropped.

## Restarts and boot

The Compose project is explicitly named `vltava` in `docker-compose.yml`; containers and the network use that prefix. This workstation's `.env` overrides `NGINX_VOLUME`, `MYSQL_VOLUME`, `REDIS_VOLUME`, and `MEILISEARCH_VOLUME` to retain the existing `unit3d_sail-*` storage after the project rename. Those are persistent data names, not a second running project. Fresh deployments default to `vltava_sail-*` volumes. Keep the overrides when restarting this deployment; removing them selects different, empty storage. Never use `docker compose down -v` on this deployment.

All compose services use `restart: unless-stopped` and pinned image tags; bump a tag deliberately and re-run `docker compose -f docker-compose.yml -f docker-compose.arr.yml up -d`. Restart policies only apply once the Docker daemon runs, so `docker.service` itself must be enabled (`sudo systemctl enable --now docker.service`); socket activation alone does not start the stack after a reboot. The workstation suspends on lid close while on battery (`HandleLidSwitch=suspend`), which takes the tracker offline.

MySQL root uses `DB_ROOT_PASSWORD` and exists only as `root@localhost`; the application and Spatie backups use `DB_USERNAME`. `.env` must stay mode `0600`.

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

## Public ingress boundary

The Cloudflare Tunnel is the public ingress. Retain the internal-only bindings for nginx origin access, MySQL, Redis, Meilisearch, SMTP, and the ARR adapter; no router port-forward or public origin address is required.
