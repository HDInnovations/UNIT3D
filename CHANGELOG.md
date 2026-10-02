# Changelog

All notable changes to this fork are documented in this file.

## Unreleased

### Added

- Canonical shared titles (`media_works`) group quality variants, music/book editions and series content scopes across catalogue layouts. Common metadata/source records appear once; swarm statistics, files, notes, downloads and accounting remain torrent-specific. Added local backfill and bounded per-title variant retrieval for SQL and Meilisearch.
- Explicit title-based metadata discovery, MusicBrainz edition selection, accessible existing-title selection, expiring trusted provider confirmations, read-only publication previews, and private owner-scoped upload drafts with file/token-safe restoration.
- Category-specific saved-metadata filters for music, games and books; edition labels/years/formats/languages/publishers remain independently searchable after sibling metadata refreshes.
- Staff metadata-quality dashboard and source-targeted, field-selective refresh with an old/new review, concurrency checks and single-use tokens. Empty raw arrays count as missing data; missing music annotations do not.

- Isolated capacity benchmark (`docker-compose.capacity.yml`, `scripts/capacity/*`): a disposable stack with its own database, Redis and search index, deterministic fixtures (10 000 users, 10 000 torrents, 40 000 peers), a k6 driver that speaks the real announce and Livewire protocols, injectable worker-restart/database-pause faults, per-phase (steady/fault/recovery) latency and error metrics, and a canary peer whose credited bytes are verified against what the driver actually sent. It never touches the live deployment.
- Token-protected `GET /health/tracker` for the PVE healthcheck: HTTP 503 with named failing checks when the tracker queue lags, a batch flush or the scheduler stalls, the database is down or failed tracker jobs exist; 404 without a configured `TRACKER_HEALTH_TOKEN`.
- `capacity:status` reports tracker/default queue depth and age, Redis batch depth, PHP-FPM pool saturation and flush-command timings as JSON for monitoring.
- Native IGDB client using Twitch OAuth; removes the unsupported IGDB Laravel wrapper.
- Laravel Reverb service, Reverb broadcast connection, and Echo 2 browser client.
- A Torznab adapter plus optional local Prowlarr, Sonarr, Radarr, and qBittorrent compose stack.
- Local production runbook, ARR integration instructions, upgrade research, and fork-maintenance roadmap.
- Czech default content for community forums, chat, wikis, information pages, and local API documentation.
- Vltava, Gruvbox, Darcula, and Catppuccin visual themes, including an OS-driven Vltava system mode.
- Grouped torrent publication and staff-release controls with contextual Czech guidance.
- Source-backed MusicBrainz release and Open Library edition metadata for music and book uploads, persisted locally after rate-limited retrieval.
- Complete provider metadata previews and saved source records: expanded IGDB references include engines, developer/publisher roles, platforms, genres, release dates, ratings, language support, media, and related games; full TMDB records retain credits and supplemental responses; MusicBrainz includes tags/genres and multi-disc tracks; Open Library retains linked author/work records; Google Books retains volume-level fields. Localized readable tables and expandable full source JSON appear during upload and on saved metadata pages. Added nullable JSON columns to existing game/movie/TV metadata tables. Book enrichment preserves Google Books-only matches rather than discarding them after upload.
- An authenticated, rate-limited upload metadata button for movie/TV TMDB IDs, game IGDB IDs, MusicBrainz releases, and book ISBN/Open Library IDs. Lookup previews show the source, cover, and actual description language; existing descriptions require confirmation before replacement. Books prefer verified Czech annotations from exact ISBN matches in Google Books or Czech Open Library editions, with explicit warnings when unavailable. Google Books accepts an optional API key; credentials remain server-side.
- Daily encrypted tracker backups replicated to PVE, a disposable database restore verifier, and an independent PVE login healthcheck.
- Public Cloudflare Tunnel ingress: a dedicated `cloudflared` LXC on PVE plus an idempotent API provisioner that publishes `tracker.iamanro.dev` without a router port-forward or public origin address.
- Live transfer statistics: the top-navigation ratio bar (upload, download, ratio, seeding, leeching) is a polled Livewire component reading uncached data, the torrent page refreshes seeders/leechers/completed from `GET /torrents/{id}/peers/counts`, and the torrent list polls its results.
- `APP_DISPLAY_TIMEZONE` (default `Europe/Prague`): every timestamp rendered in Blade is shown in the display timezone while storage and processing stay in UTC.

### Changed

- Show category-specific metadata fields directly on torrent creation before fetching, including genres, publishers, release year/date, developers, and game engines. Fetch metadata populates read-only controls and adds all remaining readable provider fields; category/identifier changes prevent displaying unrelated results. Metadata rows expose stable field keys instead of using translated labels as identifiers.
- Modernized both Vltava themes with consistent panel borders, compact corner radii, clearer navigation, roomier tables, and visible keyboard focus. The home page now offers localized browse/upload shortcuts; authentication forms follow the system light/dark preference. Fixed catalog gutters and added an accessible mobile-menu label and expanded state.
- Rebuilt the torrent history page: aligned page layout, four summary cards (uploaded, downloaded, overall ratio, actively seeding), a compact filter toolbar with removable filter chips, advanced include/exclude filters, and optional columns remembered per browser. Rows show readable transfer, ratio, and state labels with a per-row detail panel for client, timestamps, swarm, moderation, and immunity. Narrow viewports render the same rows as cards.
- Adapted torrent creation to movies, TV, games, audio/music, and XXX: category-first selection, applicable release types, category-specific metadata and technical fields, optional anime metadata, and preserved metadata when switching categories. Added XXX plus PC/Console/Lossless/Lossy/Other types through an additive migration; software/books/custom categories remain available. Hidden fields are disabled and irrelevant submitted metadata is removed server-side; incompatible category/type pairs are rejected. Filename automation respects the chosen category. The reference-data migration cannot be rolled back destructively.
- Sized the runtime from the benchmark instead of defaults: the announce and member PHP-FPM pools get the same 24-worker ceiling (measured ~41 ms per announce and ~152 ms per member roundtrip), and the tracker queue runs six workers because one single-threaded worker tops out near 24 announces/s.
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

- Stop dropping concurrent announces for the same peer. The queue's overlap lock released a blocked job immediately and consumed one of its three attempts, so simultaneous announces for one `(user, torrent)` failed with `MaxAttemptsExceededException` and their bytes were never credited; the transaction's row lock already serializes those jobs correctly. Measured on the isolated stack: 2 of 3 announces lost before, 0 after, with the full transfer credited.
- Order announces by when the tracker received the HTTP request instead of when the job happened to be constructed, so an announce delayed by passkey/torrent lookups can no longer overwrite a newer peer cursor.
- Reuse Redis sockets (`persistent` phpredis connections, one pooled socket per logical connection) instead of opening a fresh connection per request. Under announce-rate load the old behaviour exhausted the local ephemeral port range — 771 `Cannot assign requested address` failures and HTTP 500s on `/login`, `/torrents` and Livewire updates in a single 60-second benchmark; the same run now completes with zero failures.
- Keep announce credentials out of logs: the request URL recorded in exception context no longer contains the passkey.
- Replace `artisan serve` with a shared PHP-FPM/OPcache runtime. nginx now serves static files directly, gzip-compresses text responses, and caches only content-hashed build assets immutably. Queue and scheduler containers mount the current checkout again, and FastCGI preserves trusted client-IP attribution.
- Update catalogue name search, sorting, layout, page size, and pagination as result-only Livewire islands. Category changes still re-render applicable filters; the 15-second refresh returns no HTML, updates only displayed torrent statistics, and excludes torrents unapproved or deleted after the search.
- Set nginx's request-body limit to 100 MiB to match PHP instead of its implicit 1 MiB default. Valid article images above 1 MiB previously failed with HTTP 413 before reaching Laravel; per-file validation remains 10 MiB. Verified 1.41 MB PNG uploads through the real create and edit forms.
- Preserve article images when saving text without a replacement upload, show the current thumbnail in the staff edit form, and save PNG thumbnails with matching file extensions. Cover real image creation/replacement and text-only edits with regression tests.
- Separate shared-title years from names without negative year margins. Repair Work variant tables with explicit column headings/widths, full-width release names, and labelled mobile cards instead of cramped inherited table/grid layouts.
- Make expanded catalogue filters category-first and kind-aware rather than movie-centric: compatible release types, scoped video controls, dedicated music/game/book groups, accessible toggle state, and Livewire-safe region/distributor widget initialization. Clear stale hidden provider/type filters on category changes and explicit-category initial loads while preserving unscoped provider-ID links.
- Search canonical Work titles across all content kinds even when torrent filenames differ; include stored artist/author/developer/label/publisher names in the Meilisearch full-text index.

- Accept MusicBrainz album/single (`release-group`) UUIDs as well as specific `release` UUIDs during audio lookup and queued persistence. Previously valid album IDs incorrectly returned metadata not found. Preserve album source/artwork links, first release date/year, genres, and complete raw records without selecting an arbitrary edition or inventing track counts. Rename the upload/lookup identifier to `musicbrainz_id` and clarify Czech/English field guidance; provider failures remain distinct from missing records.
- Audited first-party Czech/English localization across Blade, Livewire, JavaScript, staff tools, validation, mail, notifications, themes, and shared bot/IRC messages. Separated upload/download actions from transfer totals in all 63 locale catalogues; added context-specific full-sentence translations and localized generated CSS labels.
- Reject malformed locale values without HTTP 500 responses; request language overrides preserve account/session preferences. Recipient notifications and queued mail honor the account locale, while shared broadcasts use the site default.
- Render achievement descriptions in the viewer's locale without instantiating achievements or rewriting shared metadata; canonical stored descriptions and badge filenames remain stable. Added locale-precedence and achievement-storage regression coverage plus a standalone translation catalogue audit.

- Seed the system chatroom under the name configured by `chat.system_chatroom`, so system messages no longer fail on a freshly seeded database.
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
