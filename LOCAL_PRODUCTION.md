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

`laravel.test` runs PHP-FPM with OPcache; queue, scheduler, and Reverb use the same `sail-8.4/fpm` image for CLI work. nginx serves `public/` files directly, gzip-compresses text responses, marks only content-hashed `/build/assets/*` files immutable for one year, and revalidates mutable static files. It sends application requests over FastCGI with nginx's Docker address as `REMOTE_ADDR`, preserving the `TrustProxies` boundary. The unpublished internal listener `nginx:8081` lets the Torznab adapter call the API without exposing HTTP; the adapter forwards `APP_URL`'s origin so returned links stay public.

Build or rebuild the runtime after changing Sail or `docker/php-fpm/*`:

```sh
docker compose --profile build build laravel.test
docker compose up -d --no-deps --force-recreate laravel.test nginx queue schedule reverb torznab-adapter
docker compose exec -T nginx nginx -t
```

Recreate **every** container after moving the checkout (`docker compose -f docker-compose.yml -f docker-compose.arr.yml up -d --force-recreate`); `restart` retains stale absolute bind mounts. On 2026-10-01 a container restart after the move to `app-vltava` left MySQL unable to start (its init-script bind source no longer existed), took the tracker down for about 22 hours, and silently gave Prowlarr/Radarr/Sonarr fresh empty configs at the old path. Find leftovers with `docker inspect` on each container and look for bind sources outside the current checkout. Keep the existing named-volume overrides intact. Frontend builds run as the app user: `docker compose exec -T -u sail laravel.test npm run build`.

nginx's `client_max_body_size` is explicitly 100 MiB, matching PHP-FPM's `post_max_size`/`upload_max_filesize`. Laravel keeps its stricter per-file limits (article images: 10 MiB). An nginx HTTP 413 never reaches application validation; the stock 1 MiB default rejects ordinary images.

`TMDB_API_KEY` is configured locally for metadata. Never commit `.env`.

The internal `queue` worker fetches TMDb metadata and other background jobs; six `tracker-queue` workers consume announce jobs on their own queue, so metadata work can never delay peer accounting. The `schedule` worker applies peer, history, and announce batches every five seconds. Reverb serves private and presence WebSockets internally on port 8080; nginx proxies only `/app` and `/apps` over the tracker HTTPS origin. qBittorrent has the movie library mounted read-only at `/media/movies` so it can become the initial seed without changing source media.

### Capacity measurement

`docker-compose.capacity.yml` plus `scripts/capacity/*` run a disposable benchmark stack (own MySQL, Redis, Meilisearch, nginx, PHP-FPM, queue workers, scheduler) on an internal network with no route to the internet and no published port. It never reads or writes the live database, Redis or search index.

```sh
scripts/capacity/bootstrap.sh --fresh          # schema + 10 000 users / 10 000 torrents / 40 000 peers
scripts/capacity/run-scenario.sh baseline      # 121 announces/s + 60 member requests/s for 5 minutes
scripts/capacity/run-scenario.sh sustained     # same rates for 15 minutes plus worker restart and ≤5 s MySQL pause
scripts/capacity/teardown.sh                   # removes the stack and its volumes
```

Pass `--build` to `bootstrap.sh` after changing `docker/php-fpm/*`; otherwise it reuses the local runtime image and needs no registry access. Each run writes `scripts/capacity/results/<timestamp>-<scenario>/` with the k6 summary, a `capacity:status` timeline, the canary verification log and `report.json`. Errors and latency are reported per phase (`steady`, `worker_restart`, `db_outage`, `recovery`), so an injected fault is never averaged into the steady-state result, and the run fails if the tracker queue and Redis batches do not drain.

Measured on this workstation with the live stack running beside it (benchmark slice capped at 26.625 of 32 threads, `capacity-app` 12 CPUs, Meilisearch 4, six tracker workers, both FPM pools 24 workers):

| Signal | Result |
| --- | --- |
| Announce | 85.6/s sustained, median 3.1 s, p95 3.3 s, 0 failures |
| Member requests | 24.9/s sustained, median 8.8 s, p95 11.2 s, 0 failures |
| Tracker queue | peak 2 978 jobs, oldest 27 s, fully drained after the run |
| Canary accounting | credited bytes exactly equal to what was sent |

Both FPM pools stay pinned at their ceiling for the whole run, so this is the slice's throughput limit, not a healthy operating point: the benchmark's 121 + 60 requests/s demand exceeds what 12 CPUs of PHP can serve at the measured per-request cost. Real traffic is far below this — announce intervals are 300–360 s, so 85 announces/s corresponds to roughly 25 000 active peers. The two pools are deliberately given the same worker ceiling so a saturated member site cannot take the CPU the announce path needs; raise `WEB_FPM_MAX_CHILDREN`/`TRACKER_FPM_MAX_CHILDREN` only after re-measuring.

The 15-minute `sustained` run (restart of all six tracker workers at t+300 s, MySQL paused for 5 s at t+600 s) at the same rates:

| Phase | Announces (failed) | Member requests (failed) |
| --- | --- | --- |
| Worker restart | 1 022 (0) | 492 (0) |
| MySQL pause | 327 (209 hit the 8 s client timeout) | 269 (0, median 13.7 s) |
| 30 s recovery | 3 968 (0) | 968 (0) |
| Steady | 61 233 (406, all saturation timeouts) | 19 329 (0) |

A worker restart costs nothing: jobs stay in Redis and the new workers pick them up. Announces arriving during the database pause time out, and clients simply re-announce; the first announces afterwards succeed. The tracker queue peaked at 570 jobs (oldest 10 s) and drained, MySQL never exceeded 43 connections, and the canary's credited bytes, `history` row and cursor matched exactly what was sent. The run still exits non-zero, deliberately, because the steady-state demand exceeds the slice's throughput (see above).

The flush scheduler needs real CPU. When it was capped at 0.125 CPU, each flush took 2–9 minutes, outlived its two-minute `withoutOverlapping(2)` mutex, piled up concurrent processes, got OOM-killed and pushed MySQL to its 400-connection limit. Batches then stopped draining, although nothing was lost: the backlog flushed in 12 s at about 210 MiB once given a full CPU. The live `schedule` container is not CPU-capped; never cap it below one core.

Meilisearch, not PHP, was the member-facing ceiling at low rates: at 20 requests/s it pinned a 0.75-CPU cap while PHP used 1.4 cores. Keep it on real CPU.

### Freshness of tracker statistics

Clients announce every `ANNOUNCE_INTERVAL_MIN`–`ANNOUNCE_INTERVAL_MAX` seconds (300–360 s here; libtorrent clients never go below 300 s). An announce reaches the peer and history tables within one 5-second flush; the same flush recomputes seeders/leechers for the announced torrents and re-indexes them in Meilisearch (`App\Services\TorrentPeerCountSync`). `auto:sync_peers` still runs every five minutes as a full pass for peers that expire without announcing (ghost peers are marked inactive after two hours by `auto:flush_peers`). In the browser, the ratio bar polls every 10 s, the torrent page polls `/torrents/{id}/peers/counts` every 10 s, and the visible torrent list refreshes only seeder, leecher, completion, and health counters every 15 s. That poll returns no HTML and rechecks moderation/soft-deletion for already displayed torrents. Upstream caches that remain: statistics pages 10–30 min, home page blocks 5–10 min, RSS and the torrent API 5 min.

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
- PVE runs `vltava-healthcheck.timer` every five minutes. It requests `/login` twice from outside the tracker host: once through local DNS (Caddy → bridge → workstation) and once through public DNS over DoH (Cloudflare → tunnel LXC 107 → Caddy). A third step (drop-in `vltava-healthcheck.service.d/tracker-health.conf`, public path) calls `GET /health/tracker` with the bearer token from the root-only `/etc/vltava/tracker-health.header`, matching `TRACKER_HEALTH_TOKEN` in the tracker `.env`. It returns 503, with the failing checks named in the JSON body, when the oldest tracker job is older than 60 s, a peer/history/announce flush has not finished successfully within 120 s (this also catches a dead scheduler, because flushes run every 5 s even without traffic), the database is unreachable, or `failed_jobs` holds a tracker job. Failed tracker jobs mean uncredited transfer: inspect them, then `php artisan queue:retry` or `queue:forget`; the alert persists until that is done. Without a token the route answers 404. On failure, `vltava-healthcheck-alert.service` mails root at most once per hour; that mail reaches `hello@iamanro.dev` only when the PVE notification target can deliver (see below). Investigate immediately if `systemctl status vltava-healthcheck` is failed.
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
