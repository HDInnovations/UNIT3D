# Changelog

All notable changes to this fork are documented in this file.

## Unreleased

### Added

- Native IGDB client using Twitch OAuth; removes the unsupported IGDB Laravel wrapper.
- Laravel Reverb service, Reverb broadcast connection, and Echo 2 browser client.
- A Torznab adapter plus optional local Prowlarr, Sonarr, Radarr, and qBittorrent compose stack.
- Local production runbook, ARR integration instructions, upgrade research, and fork-maintenance roadmap.
- Czech default content for community forums, chat, wikis, information pages, and local API documentation.

### Changed

- Upgraded the application stack to Laravel 13, Livewire 4, Scout 11, Intervention Image 4, Vite 8, Laravel Echo 2, and MySQL 8.4.
- Replaced Socket.IO 2 and `laravel-echo-server` with Reverb and `pusher-js`.
- Updated image uploads for Laravel's Image facade and Intervention Image 4.
- Bound tracker TLS ingress to the private LAN endpoint for Caddy termination; browser URLs and Reverb now use `https://tracker.iamanro.dev`.
- Configured PHPUnit to use its own database, cache, session store, and config-cache path.

### Fixed

- Redirect unauthenticated browser requests to the login route under Laravel 13 while preserving JSON 401 responses.
- Forward the public HTTPS port through nginx so generated login and post-login URLs do not expose the private `:8444` upstream.
- Preserve the original tracker host through Caddy to Laravel.
- Isolate PHPUnit from the production config cache so test database resets cannot affect tracker data.
- Align AutoGroup seedtime handling and refresh related test coverage.
