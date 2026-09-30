# Changelog

All notable changes to this fork are documented in this file.

## Unreleased

### Added

- Native IGDB client using Twitch OAuth; removes the unsupported IGDB Laravel wrapper.
- Laravel Reverb service, Reverb broadcast connection, and Echo 2 browser client.
- A Torznab adapter plus optional local Prowlarr, Sonarr, Radarr, and qBittorrent compose stack.
- Local production runbook, ARR integration instructions, upgrade research, and fork-maintenance roadmap.
- Czech default content for community forums, chat, wikis, information pages, and local API documentation.
- Vltava, Gruvbox, Darcula, and Catppuccin visual themes, including an OS-driven Vltava system mode.
- Grouped torrent publication and staff-release controls with contextual Czech guidance.
- Source-backed MusicBrainz release and Open Library edition metadata for music and book uploads, persisted locally after rate-limited retrieval.
- Daily encrypted tracker backups replicated to PVE, a disposable database restore verifier, and an independent PVE login healthcheck.
- Public Cloudflare Tunnel ingress: a dedicated `cloudflared` LXC on PVE plus an idempotent API provisioner that publishes `tracker.iamanro.dev` without a router port-forward or public origin address.
- Live transfer statistics: the top-navigation ratio bar (upload, download, ratio, seeding, leeching) is a polled Livewire component reading uncached data, the torrent page refreshes seeders/leechers/completed from `GET /torrents/{id}/peers/counts`, and the torrent list polls its results.
- `APP_DISPLAY_TIMEZONE` (default `Europe/Prague`): every timestamp rendered in Blade is shown in the display timezone while storage and processing stay in UTC.

### Changed

- Modernized both Vltava themes with consistent panel borders, compact corner radii, clearer navigation, roomier tables, and visible keyboard focus. The home page now offers localized browse/upload shortcuts; authentication forms follow the system light/dark preference. Fixed catalog gutters and added an accessible mobile-menu label and expanded state.
- Named the Compose project `vltava` explicitly; local volume overrides preserve the deployed database, Redis, search index, and TLS certificates across the rename.

- Upgraded the application stack to Laravel 13, Livewire 4, Scout 11, Intervention Image 4, Vite 8, Laravel Echo 2, and MySQL 8.4.
- Replaced Socket.IO 2 and `laravel-echo-server` with Reverb and `pusher-js`.
- Updated image uploads for Laravel's Image facade and Intervention Image 4.
- Bound tracker TLS ingress to the private LAN endpoint for Caddy termination; browser URLs and Reverb now use `https://tracker.iamanro.dev`.
- Configured PHPUnit to use its own database, cache, session store, and config-cache path.
- Run with `APP_ENV=production`, so Laravel again asks for confirmation before destructive database commands.
- All compose services restart automatically (`unless-stopped`) and use pinned image versions instead of `latest`.
- MySQL root has its own `DB_ROOT_PASSWORD`, is reachable only from inside the container, and no longer shares the application credential.
- `artisan serve` runs four PHP workers instead of serving one request at a time.
- Announce interval is configurable (`ANNOUNCE_INTERVAL_MIN`/`ANNOUNCE_INTERVAL_MAX`, upstream default 1800–3600 s; this deployment uses 300–360 s). The seedtime grace window follows it (1.5× max, at least 5400 s).
- Torrent seeder/leecher counters and their search-index documents are refreshed right after each announce batch instead of every 5 and 15 minutes; `auto:sync_peers` remains as a full reconciliation pass.
- nginx no longer advertises its version or `X-Powered-By`; the CSP allows the Reverb WebSocket endpoint instead of the retired Socket.IO origin (`VITE_ECHO_ADDRESS` removed).

### Fixed

- Audited first-party Czech/English localization across Blade, Livewire, JavaScript, staff tools, validation, mail, notifications, themes, and shared bot/IRC messages. Separated upload/download actions from transfer totals in all 63 locale catalogues; added context-specific full-sentence translations and localized generated CSS labels.
- Reject malformed locale values without HTTP 500 responses; request language overrides preserve account/session preferences. Recipient notifications and queued mail honor the account locale, while shared broadcasts use the site default.
- Render achievement descriptions in the viewer's locale without instantiating achievements or rewriting shared metadata; canonical stored descriptions and badge filenames remain stable. Added locale-precedence and achievement-storage regression coverage plus a standalone translation catalogue audit.

- Removed the ARR override's hardcoded `unit3d_sail` network so the full stack uses the `vltava` project network; documented the PVE-specific UFW forwarding allowance needed for browser ingress.

- Redirect unauthenticated browser requests to the login route under Laravel 13 while preserving JSON 401 responses.
- Forward the public HTTPS port through nginx so generated login and post-login URLs do not expose the private `:8444` upstream.
- Preserve the original tracker host through Caddy to Laravel.
- Stop trusting client-supplied `X-Forwarded-For` entries: Laravel trusts only the Docker hops and Caddy forwards the Cloudflare-verified client address, so announce, ban, and rate-limit IPs can no longer be spoofed.
- Staff dashboard external-tracker times used a 12-hour format without AM/PM.
- `PeerFactory` no longer writes the non-existent `torrents.id` column, so peers can be created in tests.
- Isolate PHPUnit from the production config cache so test database resets cannot affect tracker data.
- Align AutoGroup seedtime handling and refresh related test coverage.
- Add the missing `torrents.mod_queue_opt_in` column so torrent uploads no longer fail with a database error.
- Route system messages to the renamed Czech system chatroom so completed torrent uploads no longer return HTTP 500.
- Fetch TMDB movie and TV metadata in Czech, falling back to English only for missing localized fields.
- Allow owner and administrator groups to send tracker invitations without the normal two-factor activation delay.
- Skip buffered announce rows whose user or torrent no longer exists, so a single orphaned row can no longer abort every peer, history, and announce flush and freeze all tracker statistics.
