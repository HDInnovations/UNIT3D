# UNIT3D Major Stack Upgrade Research

Retrieval date for all cited sources: 2026-09-28. This is a planning report only; it does not prescribe applying changes before disposable test infrastructure and verified restorable backups exist.

## Executive recommendation

Proceed in phases, not as one dependency jump:

1. **Recovery guardrail first:** build a disposable clone of production-like infrastructure, take full database backups, and prove restore before any migration rehearsal. MySQL explicitly requires a pre-upgrade backup and states rollback from MySQL 8.4 to earlier 8.4/8.3 releases is unsupported except by restoring a backup.[^mysql-before]
2. **PHP 8.4 readiness:** keep PHP 8.4 as the platform target because Laravel 12 and 13 both support it, but audit PHP 8.4 incompatibilities/deprecations before production use.[^laravel13-releases][^php84-migration]
3. **Backend framework major:** upgrade Laravel 12 to 13 and first-party PHP packages, then address Laravel 13 behavior changes.
4. **Database separately:** upgrade MySQL 8.0 to MySQL 8.4 LTS first; only after a stable 8.4 run should a separate 8.4 LTS to 9.7 LTS plan be considered. MySQL documents 8.0 to 8.4 as the next-LTS path and does not support skipping LTS series.[^mysql-paths][^mysql-releases]
5. **Broadcasting decision:** because the broadcast server is not deployed and broadcasting is dormant, prefer removing the legacy `laravel-echo-server` / Socket.IO 2 path after confirming no browser code attempts to connect and no active `ShouldBroadcast` workflows require delivery. If realtime is revived, migrate to Laravel Reverb, Pusher Channels, or Ably; these are Laravel's documented broadcast drivers, and Reverb is Laravel's first-party WebSocket server.[^laravel-broadcasting][^laravel-reverb][^echo-server-package]
6. **Frontend/tooling:** move Vite 6 -> 7 -> 8, then Laravel Echo 1 -> 2 / Socket.IO 2 -> 4 only if Socket.IO remains. Vite 7 and 8 both have major runtime/build changes; Socket.IO recommends staged server/client rollout for 2 -> 3/4 compatibility.[^vite7][^vite8][^socket-client][^socket-2-3]
7. **Livewire last among UI behavior changes:** upgrade Livewire 3 -> 4 after framework/tooling smoke tests, because it changes component config, routing recommendations, `wire:model` semantics, component tag parsing, endpoints, and JS hook APIs.[^livewire4]

## Source-cited upgrade matrix

| Area | Current -> target | Confirmed requirements / compatibility | Breaking changes and risks | Recommendation |
|---|---:|---|---|---|
| PHP | 8.4 stays target | PHP 8.4 is supported by PHP until 2028-12-31 for security fixes; Laravel 12 supports PHP 8.2-8.5 and Laravel 13 supports 8.3-8.5.[^php-supported][^laravel13-releases] | PHP 8.4 changes `exit()`/`die()` behavior, removes the `E_STRICT` error level, converts several resources to objects, tightens many `ValueError`/`TypeError` cases, removes some MySQLi constants, and changes MySQLnd timeout error code behavior for MySQL >=8.0.24.[^php84-incompat] Deprecated patterns include implicitly nullable parameters, `trigger_error(..., E_USER_ERROR)`, several MySQLi methods, default CSV escape parameters, and more.[^php84-deprecated] | Run static/code audit and dependency resolution on PHP 8.4 before any data migration. Treat deprecations as upgrade blockers where they touch hot paths. |
| Laravel | 12.69.2 -> 13.x | Laravel 13 requires PHP >=8.3 and updates `laravel/framework` to `^13.0`; the upgrade guide also lists `laravel/tinker:^3.0`, `phpunit/phpunit:^12.0`, and `pestphp/pest:^4.0` as dependency targets.[^laravel13-upgrade][^laravel13-composer] | High/medium risk items: CSRF middleware formalized as `PreventRequestForgery` with deprecated aliases; cache `serializable_classes` hardening; MySQL/MariaDB `upsert` now rejects empty `uniqueBy`; session serialization config may invalidate active sessions if moved from `php` to `json`; joined MySQL deletes may now include `ORDER BY`/`LIMIT` and can error on unsupported syntax.[^laravel13-upgrade] | Upgrade after PHP audit. Search app/tests for direct CSRF middleware class references, empty `upsert` `uniqueBy`, custom cache/session object serialization, queue event listeners, custom contracts/drivers, and joined deletes. |
| Laravel Scout | 10.25.0 -> 11.x | Scout 11 still allows PHP `^8.0` and Illuminate 9-13 components; latest 11.x adds Laravel AI suggestion support and maintains Algolia/Meilisearch/Typesense driver suggestions.[^scout11-composer] | Scout 11.0.0 release notes include removal of numeric filters and a change to use the Scout prefix when deleting all indexes; Scout docs warn Meilisearch users to review Meilisearch service breaking changes when upgrading Scout.[^scout110][^scout-docs] | Upgrade alongside Laravel 13. Rehearse index delete/import behavior on disposable search infrastructure; do not run destructive index commands against production until backup/rebuild path is proven. |
| Livewire | 3.8.10 -> 4.x | Livewire 4 supports PHP `^8.1` and Illuminate/Laravel 10-13 components.[^livewire4-composer] | High-impact changes: config key renames, new component locations/namespaces, `smart_wire_keys` default, `Route::livewire()` recommendation/requirement for view-based full-page components, `wire:model` no longer listens to child events unless `.deep`, `wire:scroll` becomes `wire:navigate:scroll`, and component tags must close.[^livewire4] Medium/low changes include `.blur`/`.change` client-state timing, `wire:transition` using View Transitions API with modifiers removed, stream/mount signature changes, hash-prefixed Livewire URLs, and JS hook deprecations.[^livewire4] | Defer until backend and Vite are stable. Audit Blade for unclosed `<livewire:...>` tags, `wire:transition.*`, container-level `wire:model`, custom Livewire endpoints/CDN/firewall rules, and JS hooks. |
| Laravel Echo | 1.19 -> 2.x | Echo 2.5.0 package metadata shows ESM packaging, Node engine `^20.19.0 || >=22.12.0`, and optional peer dependencies for `pusher-js` and `socket.io-client`; dev dependency is `socket.io-client:^4.8.3`.[^echo-package] | Echo 2.0.0 release notes describe major package upgrades, better TypeScript support, and smaller build.[^echo200] ESM / Node engine changes can break older asset toolchains. | If broadcasting remains dormant, remove Echo initialization/deps only after confirming no runtime client connect path. If keeping Echo, align with Vite 8/Node 20.19+ and either Reverb/Pusher (`pusher-js`) or Socket.IO 4 (`socket.io-client`). |
| Socket.IO | server 2.5.1 / client 2.3.1 -> 4.x | Socket.IO latest docs list 4.8.3 as latest and show JS client/server compatibility: v2 clients can connect to v3/v4 servers only with `allowEIO3: true`; v3/v4 clients do not connect to v2 servers.[^socket-client] | 2 -> 3 changes include CORS disabled by default, no default cookie, API removals/renames, rooms becoming Sets, named ESM import, no chained `emit()`, no IE8/Node 8 support, and staged production rollout requirements.[^socket-2-3] 3 -> 4 adds server-side API breaks such as immutable `io.to()` and `wsEngine` option changes; v3/v4 protocol remains compatible.[^socket-3-4] | Prefer replacement/removal. If Socket.IO remains, upgrade server first with `allowEIO3:true`, then clients, then disable `allowEIO3`; do not mix v4 clients with v2 server. |
| `laravel-echo-server` | 1.6.3 -> remove or replace | Laravel broadcasting docs list Reverb, Pusher Channels, and Ably as supported server-side drivers; the quickstart also documents a `log` driver and a `null` driver for disabled broadcasting in testing.[^laravel-broadcasting] Reverb is first-party and integrates with Laravel broadcasting.[^laravel-reverb] | `laravel-echo-server` 1.6.3 depends on `socket.io:^2.3.0`, `request:^2.88.2`, `ioredis:^4.16.0`, and its README lists Laravel 5.3, Node 6.0+, and Redis 3+ as requirements.[^echo-server-package][^echo-server-readme] That stack is far behind the current app and Socket.IO 4. | Since the server is not deployed and broadcasting is dormant, removal is acceptable after verifying no deployed process, no required Redis broadcast consumer, and no frontend socket connection. If realtime is needed, implement Reverb rather than modernizing `laravel-echo-server`. |
| Vite | 6.4.3 -> 8.x current | Vite main package is 8.3.1 and requires Node `^20.19.0 || >=22.12.0`.[^vite-package] Vite 7 already requires Node 20.19+ / 22.12+.[^vite7] | Vite 7 removes Node 18 support, changes default browser target, removes Sass legacy API support and deprecated features.[^vite7] Vite 8 switches from esbuild/Rollup internals to Rolldown/Oxc, deprecates `optimizeDeps.esbuildOptions` and `esbuild`, changes JS/CSS minification, changes CJS interop, removes format-sniffing module resolution, and removes `build.rollupOptions.watch.chokidar`.[^vite8] | Upgrade Node/tooling first, then Vite 7, then Vite 8. Audit custom Vite config/plugins for esbuild/Rollup assumptions and Sass legacy API. |
| MySQL | 8.0 -> 8.4 LTS, then evaluate 9.7 LTS | MySQL 8.0 entered Oracle Sustaining Support on 2026-04-21, and users are encouraged to upgrade to MySQL 8.4 LTS or 9.7 LTS.[^mysql-eol] MySQL supported-platforms list both 8.4 LTS and 9.7 LTS.[^mysql-platforms] MySQL upgrade paths support 8.0 -> 8.4 LTS; skipping LTS series is not supported.[^mysql-paths][^mysql-releases] | Pre-upgrade backup is mandatory; downgrading from 8.4 to 8.3 or earlier 8.4 is unsupported except by restoring a pre-upgrade backup.[^mysql-before] MySQL 8.4 disables `mysql_native_password` by default and changes many InnoDB defaults.[^mysql84-new] | For UNIT3D's recovery context, make MySQL the most controlled phase: restore-verified backup, clone rehearsal, MySQL Shell Upgrade Checker, measure runtime/perf on 8.4, then decide whether 9.7 LTS is worth a second phase. |

## Confirmed requirements

- Laravel 13 is compatible with PHP 8.4 because Laravel 13 supports PHP 8.3-8.5; Laravel 12 also supports PHP 8.2-8.5.[^laravel13-releases]
- Laravel 13 upgrade requires at least `laravel/framework:^13.0`; it also lists test/tooling dependency targets for PHPUnit and Pest that already match or exceed the current major in this repository context.[^laravel13-upgrade]
- Livewire 4 can run with Laravel 13 and PHP 8.4 based on its composer constraints.[^livewire4-composer]
- Scout 11 can run with Illuminate/Laravel 13 and PHP 8.4 based on its composer constraints.[^scout11-composer]
- Echo 2 / Vite 8 require a modern Node runtime (`^20.19.0 || >=22.12.0`).[^echo-package][^vite-package]
- Socket.IO 4 client/server migration from v2 cannot be done by only updating clients against a v2 server; v2 clients can be temporarily supported by v3/v4 servers with `allowEIO3:true`, then phased out.[^socket-client][^socket-2-3]
- MySQL 8.0 should be upgraded to 8.4 LTS first; moving to 9.7 LTS is a distinct later hop.[^mysql-paths][^mysql-releases]

## Inferences for UNIT3D

- **[INFERENCE]** Because the broadcast server is not deployed and broadcasting is dormant, removing `laravel-echo-server`, Socket.IO server dependencies, and Socket.IO client code is lower risk than modernizing that path, provided the app is first audited for active `ShouldBroadcast` events and frontend Echo initialization.
- **[INFERENCE]** If the app currently only has Redis broadcast configuration but no running broadcast server, existing broadcast events are already non-user-visible; switching future dormant environments to no-op/log broadcasting should be part of the removal plan.
- **[INFERENCE]** MySQL should not be bundled with the Laravel/PHP/Livewire/Vite upgrade window because failed database rollback depends on backup restore, not package downgrade.
- **[INFERENCE]** Vite 8 should be after Echo/broadcasting direction is chosen; if Socket.IO is removed, frontend dependency resolution and bundle compatibility become simpler.

## Unknowns that require repository/runtime audit before implementation

- Whether UNIT3D has active `ShouldBroadcast`, `ShouldBroadcastNow`, private/presence channels, Echo JS initialization, or UI features that silently rely on realtime delivery.
- Whether custom Laravel cache stores, queue drivers, response factories, route/domain ordering, job event listeners, or middleware references touch Laravel 13 breaking points.
- Whether application code stores PHP objects in cache/session, uses empty `upsert` `uniqueBy`, relies on named Laravel method arguments, or depends on old pagination/bootstrap view names.
- Whether any package outside the named stack blocks Laravel 13, PHP 8.4, Node 20.19+, Vite 8, or MySQL 8.4/9.7.
- Whether production data contains MySQL accounts still using `mysql_native_password`, configuration values removed/deprecated by MySQL 8.4, or queries affected by changed InnoDB defaults/performance.

## Ordered migration prerequisites and rollback/data-risk checklist

1. **Backups and test infrastructure**
   - Create disposable app/database/search/Redis infrastructure.
   - Take full MySQL backup including the `mysql` system database and prove restore into the disposable environment before any upgrade rehearsal.[^mysql-before]
   - Capture current schema, indexes, MySQL users/plugins/auth methods, queue/search/broadcast settings, and asset build runtime.
2. **Static audits before upgrades**
   - PHP 8.4: implicit nullable parameters, deprecated MySQLi/session/CSV patterns, code relying on `E_STRICT`, `is_resource()` checks for extensions changed to objects, and stricter `ValueError`/`TypeError` cases.[^php84-incompat][^php84-deprecated]
   - Laravel 13: CSRF middleware references, cache/session serialization, `upsert`, queue event listeners, custom contracts/drivers, route domain precedence, joined MySQL deletes.[^laravel13-upgrade]
   - Livewire: Blade component tags, `wire:model` container usage, `wire:transition.*`, `wire:scroll`, custom endpoints/CDN/firewall rules, JS hooks.[^livewire4]
   - Vite/Echo: Node version, ESM/CJS imports, Vite plugins, Sass legacy API, esbuild/Rollup assumptions.[^vite7][^vite8]
   - Broadcasting: no deployed `laravel-echo-server`, no required Redis subscriber, no active browser connection or realtime feature.
3. **Database rehearsal**
   - Run MySQL Shell Upgrade Checker as recommended by MySQL before upgrade.[^mysql-paths]
   - Rehearse 8.0 -> 8.4 LTS restore/upgrade on disposable data; record duration, errors, changed auth plugins, and performance deltas.
   - Treat rollback as restore-from-backup, not version downgrade.[^mysql-before]
4. **Application rehearsal**
   - Resolve Composer/npm dependency plans without changing production.
   - Upgrade PHP/Laravel/Scout in a branch/environment, then Livewire/Vite/Echo according to the chosen broadcasting path.
   - If retaining Socket.IO, follow official staged rollout: v4 server with `allowEIO3:true`, client updates, then `allowEIO3:false`.[^socket-2-3]
5. **Production cutover plan**
   - Freeze writes or use a proven replication/blue-green strategy for MySQL.
   - Verify backup freshness and restore command before cutover.
   - Keep old application image and database backup paired; package rollback alone is insufficient after MySQL upgrade.
   - Predefine abort points: dependency install failure, migration failure, MySQL upgrade checker failures, auth plugin incompatibility, search index rebuild failure, or asset build/runtime failure.

## Concrete phased plan

- **Phase 0: Recovery hardening.** Disposable environment + backup/restore proof. No runtime/dependency upgrades.
- **Phase 1: Compatibility cleanup.** PHP 8.4 and Laravel 13 static fixes while still on current stack; add targeted checks for Laravel/Livewire/Vite patterns.
- **Phase 2: Laravel 13 + Scout 11.** Upgrade PHP package stack; do not change MySQL major/LTS or broadcast architecture in the same deploy.
- **Phase 3: Broadcasting simplification.** If audit confirms dormancy, remove `laravel-echo-server` and Socket.IO path; otherwise migrate to Reverb and `pusher-js`/Echo config. Avoid investing in a Socket.IO 2 -> 4 server unless there is an active realtime requirement.
- **Phase 4: Frontend majors.** Node 20.19+; Vite 6 -> 7 -> 8; Echo 2 if retained; Livewire 4 UI behavior changes after asset tooling is stable.
- **Phase 5: MySQL 8.0 -> 8.4 LTS.** Separate data migration window with restore-tested backup and Upgrade Checker output. Evaluate 9.7 LTS only after stable 8.4 operation.

[^laravel13-releases]: Laravel, "Release Notes" / support policy, https://laravel.com/docs/13.x/releases (retrieved 2026-09-28).
[^laravel13-upgrade]: Laravel, "Upgrade Guide: Upgrading to 13.0 from 12.x", https://laravel.com/docs/13.x/upgrade (retrieved 2026-09-28).
[^laravel13-composer]: Laravel Framework `13.x` `composer.json`, https://raw.githubusercontent.com/laravel/framework/13.x/composer.json (retrieved 2026-09-28).
[^laravel-broadcasting]: Laravel, "Broadcasting", supported drivers / quickstart / client installation, https://laravel.com/docs/12.x/broadcasting (retrieved 2026-09-28).
[^laravel-reverb]: Laravel, "Laravel Reverb", https://laravel.com/docs/12.x/reverb (retrieved 2026-09-28).
[^scout11-composer]: Laravel Scout `11.x` `composer.json`, https://raw.githubusercontent.com/laravel/scout/11.x/composer.json (retrieved 2026-09-28).
[^scout110]: Laravel Scout v11.0.0 release notes, https://github.com/laravel/scout/releases/tag/v11.0.0 (retrieved 2026-09-28).
[^scout-docs]: Laravel Scout documentation, Meilisearch warning, https://laravel.com/docs/12.x/scout (retrieved 2026-09-28).
[^livewire4]: Livewire, "Upgrade Guide" 4.x, https://livewire.laravel.com/docs/4.x/upgrading (retrieved 2026-09-28).
[^livewire4-composer]: Livewire `4.x` `composer.json`, https://raw.githubusercontent.com/livewire/livewire/4.x/composer.json (retrieved 2026-09-28).
[^echo-package]: Laravel Echo 2.x package metadata, https://raw.githubusercontent.com/laravel/echo/2.x/packages/laravel-echo/package.json (retrieved 2026-09-28).
[^echo200]: Laravel Echo v2.0.0 release notes, https://github.com/laravel/echo/releases/tag/v2.0.0 (retrieved 2026-09-28).
[^echo-server-package]: `laravel-echo-server` package metadata, https://raw.githubusercontent.com/tlaverdure/laravel-echo-server/master/package.json (retrieved 2026-09-28).
[^echo-server-readme]: `laravel-echo-server` README, https://raw.githubusercontent.com/tlaverdure/laravel-echo-server/master/README.md (retrieved 2026-09-28).
[^socket-client]: Socket.IO, "Client Installation" / version compatibility, https://socket.io/docs/v4/client-installation/ (retrieved 2026-09-28).
[^socket-2-3]: Socket.IO, "Migrating from 2.x to 3.0", https://socket.io/docs/v4/migrating-from-2-x-to-3-0/ (retrieved 2026-09-28).
[^socket-3-4]: Socket.IO, "Migrating from 3.x to 4.0", https://socket.io/docs/v4/migrating-from-3-x-to-4-0/ (retrieved 2026-09-28).
[^vite7]: Vite, "Migration from v6" in Vite 7 docs, https://v7.vite.dev/guide/migration.html (retrieved 2026-09-28).
[^vite8]: Vite, "Migration from v7", https://vite.dev/guide/migration.html (retrieved 2026-09-28).
[^vite-package]: Vite package metadata, https://raw.githubusercontent.com/vitejs/vite/main/packages/vite/package.json (retrieved 2026-09-28).
[^mysql-releases]: MySQL 8.4 Reference Manual, "MySQL Releases: Innovation and LTS", https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html (retrieved 2026-09-28).
[^mysql-platforms]: MySQL, "Supported Platforms: MySQL Database", https://www.mysql.com/support/supportedplatforms/database.html (retrieved 2026-09-28).
[^mysql-eol]: MySQL, "MySQL Product Support EOL Announcements", https://www.mysql.com/support/eol-notice.html (retrieved 2026-09-28).
[^mysql-before]: MySQL 8.4 Reference Manual, "Before You Begin", https://dev.mysql.com/doc/refman/8.4/en/upgrade-before-you-begin.html (retrieved 2026-09-28).
[^mysql-paths]: MySQL 8.4 Reference Manual, "Upgrade Paths", https://dev.mysql.com/doc/refman/8.4/en/upgrade-paths.html (retrieved 2026-09-28).
[^mysql84-new]: MySQL 8.4 Reference Manual, "What Is New in MySQL 8.4 since MySQL 8.0", https://dev.mysql.com/doc/refman/8.4/en/mysql-nutshell.html (retrieved 2026-09-28).
[^php-supported]: PHP, "Supported Versions", https://www.php.net/supported-versions.php (retrieved 2026-09-28).
[^php84-migration]: PHP Manual, "Migrating from PHP 8.3.x to PHP 8.4.x", https://www.php.net/manual/en/migration84.php (retrieved 2026-09-28).
[^php84-incompat]: PHP Manual, "PHP 8.4 Backward Incompatible Changes", https://www.php.net/manual/en/migration84.incompatible.php (retrieved 2026-09-28).
[^php84-deprecated]: PHP Manual, "PHP 8.4 Deprecated Features", https://www.php.net/manual/en/migration84.deprecated.php (retrieved 2026-09-28).
