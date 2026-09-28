# Research Note: Arcadia vs UNIT3D for this deployment

Date: 2026-09-28
Scope: Evaluate whether migrating this locally deployed private tracker from **UNIT3D** (`HDInnovations/UNIT3D`, currently running in this repository) to **Arcadia** is justified, using only first-party sources (official repos, docs, releases).

## 1. Project identification

- **UNIT3D** — currently deployed here. First-party source: https://github.com/HDInnovations/UNIT3D (README: "UNIT3D is a private torrent tracker built using Laravel, Livewire and AlpineJS.").
- **Arcadia** — the only plausible open-source "Arcadia" torrent-tracker project found under first-party control is https://github.com/Arcadia-Solutions/arcadia ("Content-agnostic torrent site & tracker framework"), org: https://github.com/Arcadia-Solutions. Docs: https://arcadia-solutions.github.io/arcadia/. No other first-party "Arcadia" torrent-tracker project was found; several GitHub results were unrelated forks/mirrors of this same repo, so ambiguity is low.

## 2. Facts (primary sources, retrieved via GitHub API / raw README / docs, 2026-09-28)

| Dimension | UNIT3D | Arcadia |
|---|---|---|
| Repo | `HDInnovations/UNIT3D` | `Arcadia-Solutions/arcadia` |
| License | AGPL-3.0 | AGPL-3.0 |
| Created | 2017-12-07 | 2025-03-11 |
| Last push | 2026-09-28 | 2026-09-28 |
| Stars / forks | 2,432 / 450 | 359 / 62 |
| Open issues | 173 | 70 |
| Primary language | PHP (Laravel 12, Livewire, AlpineJS; MySQL, "MySQL Strict Mode Compliant"; PHP 8.4-ready) | Rust backend (actix-web + sqlx + PostgreSQL); TypeScript/Vue.js (PrimeVue, Vite) SPA frontend |
| Tagged releases | Frequent, e.g. `v9.2.0` (2025-12-03), `v9.1.7`…`v9.1.2` roughly monthly through 2025 | Single GitHub release `0.0.1` (2025-04-22); no newer tag since, despite continuous commit activity |
| Maintainer statement | No explicit "pre-alpha" disclaimer in README | README states: *"Development is going well, but I am currently actively looking for other devs to hop into the project!"* — signals a small/early team and pre-production maturity |
| Installer | README: *"The official script is no longer available at this time. A new one will be provided soon."* (as of retrieval date) | `compose.yml` + `config.example.yml` in repo; docs site has `run-docker.md`/`run-standard.md` install guides |
| Architecture | Monolithic Laravel MVC app; tracker/announce handled by a **separate first-party companion project**, `Roardom/UNIT3D-Announce` (Rust, 62 stars, "High-performance private BitTorrent tracker compatible with UNIT3D tracker software") | Documented as "2 main parts: the site's API and a tracker" (`docs/src/architecture.md`), both **in the same repository** (`backend/` REST API, `tracker/arcadia_tracker/` announce service) |
| Announce/tracker support | HTTP(S) + UDP announce via UNIT3D-Announce, decoupled service, independently deployable/scalable | Own `arcadia_tracker` binary written for torrent clients (qBittorrent, Deluge, etc., per docs); Arcadia's README explicitly credits UNIT3D-Announce as an influence: *"Thanks to UNIT3D-Announce for their amazing tracker and help explaining its details"* |
| Metadata/content model | Torrent categories/types configured per-deployment (Laravel app, DB-driven) | Docs describe first-class typed content models out of the box: Movies, TV Shows, Music (Album/EP/Single/Soundtrack/Anthology/Compilation/Remix/Bootleg/Mixtape/Concert/DJ Mix), Software (Game/Program), Written Documents (Book/Illustrated/Periodical/Article/Manual), plus "Collections" (grouping mechanism for site dumps, series, periodic drops) |
| API / automation | No first-party OpenAPI/Swagger artifact found in README; automation historically via app-specific routes/Livewire (not verified against a machine-readable schema in this research) | REST API is generated with a documented, versioned schema: Swagger UI at `/swagger-ui/`, OpenAPI JSON at `/swagger-json/openapi.json`; frontend TypeScript client is code-generated from that schema via `openapi-generator-cli` (documented workflow in `docs/src/architecture.md`) |
| CI/quality signals | Multiple GitHub Actions badges in README: lint, PHPUnit tests, asset compile test, Larastan (static analysis), Prettier-Blade | `.coderabbit.yaml` present (AI code review bot) and `.cargo-husky` (pre-commit hooks) in repo tree; no CI badge matrix comparable to UNIT3D's was found in the retrieved README |
| Translations | Weblate-integrated (https://hosted.weblate.org/engage/unit3d/) | Not found in retrieved README/docs |
| Community/support | Discord (paid support tier for HDInnovations services); active PR/issue flow | Discord + Matrix channels; actively recruiting additional developers |

Sources:
- https://github.com/HDInnovations/UNIT3D (README, retrieved via raw content)
- https://github.com/HDInnovations/UNIT3D/releases
- https://github.com/Roardom/UNIT3D-Announce
- https://github.com/Arcadia-Solutions/arcadia (README, retrieved via raw content)
- https://github.com/Arcadia-Solutions/arcadia/releases and /tags
- https://arcadia-solutions.github.io/arcadia/ (Introduction page)
- https://raw.githubusercontent.com/Arcadia-Solutions/arcadia/main/docs/src/architecture.md
- GitHub REST API (`api.github.com/repos/...`) for stars/forks/dates/issues, retrieved 2026-09-28

## 3. Migration/operational implications for this deployment (inference, clearly separated from facts above)

- **[INFERENCE]** This repository is a mature, customized UNIT3D deployment (Laravel 12, PHP 8.4, existing `docker-compose.yml`, `ARR.md` *arr-stack integration, `LOCAL_PRODUCTION.md` runbook, PHPStan baseline, Pint config, full test suite). All of that operational tooling, plus any local data/schema/content already in MySQL, would need to be rebuilt or migrated to Arcadia's PostgreSQL + Rust/Vue stack — there is no documented UNIT3D→Arcadia data-migration tool in either repo.
- **[INFERENCE]** Arcadia's single `0.0.1` release, absence of a stable installer/upgrade story comparable to UNIT3D's `php artisan git:update`, and the maintainer's own call for more contributors indicate it is an early-stage/pre-1.0 project, not yet positioned as a drop-in production replacement for an established tracker.
- **[FACT]** Arcadia is architecturally more modern in isolated respects (typed content model, in-repo tracker, schema-generated API client) and has significant commit velocity, but this does not by itself offset the operational cost of a stack rewrite (PHP→Rust, MySQL→PostgreSQL, Blade/Livewire→Vue SPA) for an already-working, actively maintained UNIT3D instance.

## 4. Recommendation

**Migration to Arcadia is not justified for this deployment at this time.**

Rationale: UNIT3D is the incumbent, has ~7x the community size (stars/forks), a much longer release history with regular tagged versions, mature CI, translation infrastructure, and an existing local production setup already integrated into this repository. Arcadia is a promising, actively developed Rust/Vue alternative with some architectural advantages (unified typed content model, documented OpenAPI schema, in-repo tracker), but it is still pre-1.0 (single `0.0.1` release), lacks a documented data-migration path from UNIT3D, and its own maintainers are still recruiting core developers — all first-party signals of insufficient production maturity relative to the cost of a full stack migration for a working local private tracker.

Recommended action: continue on UNIT3D; optionally track Arcadia's release cadence (watch for a 1.0/stable tag and a documented migration tool) before revisiting this decision.
