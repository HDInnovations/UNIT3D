# UNIT3D maintenance-fork roadmap

**Fork:** [`iamanro/UNIT3D`](https://github.com/iamanro/UNIT3D)
**Upstream:** [`HDInnovations/UNIT3D`](https://github.com/HDInnovations/UNIT3D)
**Scope:** Own and verify fixes needed by this tracker; keep upstream compatibility where it does not conflict with correctness or privacy.

## Operating rules

- `origin` is the maintained fork; `upstream` is the read-only source of upstream changes.
- Each remediation gets an isolated branch and a regression that exercises the consumer-visible failure.
- Carry a patch locally only when upstream has no merged fix. Link the upstream issue in the branch and commit.
- Before pulling an upstream release, review whether it includes or conflicts with each carried patch. Never assume an open issue is fixed.
- Do not add new tracker-specific behavior to UNIT3D when it belongs in the Torznab adapter or deployment layer.

## Priority 0 — preserve privacy and current operation

### Anonymous-post identity leakage — [UNIT3D #5297](https://github.com/HDInnovations/UNIT3D/issues/5297)

**Risk:** Quote BBCode can reveal the real username of an anonymous author.

**Decision:** Keep anonymous forum/comment posting disabled until the upstream fix ships or the forked fix is verified.

**Fork change if enabled:** Resolve the quoted author through the same anonymous-display policy used by normal post rendering; never serialize the underlying username into quote markup.

**Verification:** Create an anonymous post, quote it as a non-staff member, and assert that neither the HTML nor generated BBCode contains the real username.

**Exit:** Upstream merged/released fix verified against the regression; then drop the carried patch.

### Preserve current custom integration boundary

**Risk:** Native Torznab is explicitly not planned upstream ([#4398](https://github.com/HDInnovations/UNIT3D/issues/4398)).

**Decision:** Keep UNIT3D core free of Torznab behavior. Maintain category mapping, API-key validation, search caching, and polling limits in `arr-adapter/`.

**Verification:** Existing Torznab caps/search/error smoke scenarios remain green after every UNIT3D upgrade.

## Priority 1 — tracker correctness under automation

### Burst-safe IRC announces — [UNIT3D #3785](https://github.com/HDInnovations/UNIT3D/issues/3785)

**Risk:** Rapid API/autouploader activity can break IRC writes and silently lose chat announces while the torrent record itself succeeds.

**Fork change:** Move IRC announce delivery to a dedicated queued job. Serialize by IRC target with `WithoutOverlapping`; give failures bounded retry/backoff and log a durable failure rather than failing the upload request.

**Verification:** Submit a deterministic burst of uploads/announce jobs to a test IRC transport. Every accepted torrent produces exactly one successful delivery or one observable failed job; the API upload response remains successful and duplicate-free.

**Upstream strategy:** Offer this as a narrow PR because the maintainer already proposed the same queue-based direction.

### Correct promotion seedtime — [UNIT3D #5116](https://github.com/HDInnovations/UNIT3D/issues/5116)

**Risk:** User-facing eligibility and `AutoGroup` can calculate average seedtime from different history sets.

**Fork change:** Apply the documented query alignment so both paths use the same deleted-torrent inclusion rule. Do not change promotion criteria beyond this defect.

**Verification:** Fixture with active and deleted torrents; dashboard eligibility and `AutoGroup` must produce the same average and promotion decision.

**Upstream strategy:** Rebase the community patch named in the issue onto the current release, then submit a regression-backed PR.

### Bound adapter polling — [UNIT3D #4901](https://github.com/HDInnovations/UNIT3D/issues/4901)

**Risk:** UNIT3D has no tracker-side API for automatically regulating indexer hammering.

**Decision:** Fix outside the fork in `arr-adapter/`: cache `caps`, apply a bounded per-token/search rate limiter, and return a Torznab-compliant transient error when over limit.

**Verification:** Burst a fixed search workload; prove UNIT3D receives no more than the configured rate and each client gets deterministic success or transient response.

## Priority 2 — operational dependency removal

### Replace EOL realtime service — [UNIT3D #4875](https://github.com/HDInnovations/UNIT3D/issues/4875)

**Risk:** `laravel-echo-server` is EOL and acknowledged upstream as a security/supply-chain risk.

**Decision:** Do not introduce the realtime chat/notifier service in this deployment. Evaluate a separate migration spike to Laravel Reverb or Sockudo only after defining required chat and notification contracts.

**Verification before cutover:** authenticated websocket connection, room authorization, private notification delivery, reconnect behavior, rate limits, and load test. No production cutover without all gates.

**Upstream strategy:** Prefer upstream's official migration when released; maintain a fork-only migration only if realtime becomes a product requirement before then.

## Priority 3 — low-risk scheduled-job and admin fixes

### Do not notify disabled accounts — [UNIT3D #4869](https://github.com/HDInnovations/UNIT3D/issues/4869)

**Fork change:** Make the listed scheduled notification queries exclude banned, pruned, and disabled users before dispatching mail.

**Verification:** One active and one disabled fixture; only the active account receives notification.

### Admin pruning and forum navigation — [#3970](https://github.com/HDInnovations/UNIT3D/issues/3970), [#4082](https://github.com/HDInnovations/UNIT3D/issues/4082)

**Decision:** Reproduce before assigning work. Both are low-impact, single-report admin/UX defects; do not patch from issue text alone.

## Upgrade gate

For each upstream release:

1. Read release notes and linked merged PRs.
2. Run the carried-patch regression suite and tracker/ARR smoke scenarios.
3. Drop only patches whose upstream replacement passes the same regression.
4. Record the chosen upstream tag and remaining carried patch list in the release PR.

## Activation audit — 2026-09-28

- **Anonymous forum posts (#5297): active latent risk.** The topic/reply templates render an anonymous checkbox and the controllers accept `anon`; there is no feature flag to disable it. The database currently has zero anonymous posts. No safe configuration-only mitigation exists: keep the option operationally unused until the fork patch is regression-tested, before inviting non-staff members.
- **IRC announce drops (#3785): dormant.** Both built-in IRC and external IRC announce integrations resolve to disabled. Do not enable either before the queued-delivery fix exists.
- **Seedtime promotions (#5116): active configuration risk.** Nine groups have `autogroup` enabled. `Seeder` requires 30 days and `Archivist` 60 days average seedtime, so the inconsistent calculation can affect future promotions. Schedule the regression-backed query alignment before relying on those promotions.
- **EOL realtime layer (#4875): dormant.** Broadcasting is configured for Redis, but the deployment has no Echo/Reverb/Sockudo service. Do not add an Echo server.
- **Indexer hammering (#4901): active capacity gap.** The current Torznab adapter forwards each search directly to UNIT3D with no cache or rate limiter. Add adapter-side caps caching, bounded per-token search rate, and transient-overload responses before adding more clients or exposing the source publicly.

## Current status

No remediation code is committed or pushed by this roadmap. The fork is established; planned work begins with the regression-backed #5116 promotion fix and adapter-side rate limits. The anonymous-posting patch must land before non-staff members are invited.
