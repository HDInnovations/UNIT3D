// Real authenticated web traffic: a genuine Fortify session login (CSRF
// cookie/token flow) + one real GET /torrents per VU to establish Livewire
// component state (both one-time setup costs, reported under a separate
// `web_auth_init_ms` metric, not folded into the rate-counted roundtrips).
// Every counted iteration is exactly ONE real request, weighted across the
// actual registered routes/protocol:
//   1/3 catalogue  - plain GET /torrents (full page reload)
//   1/6 search     - real Livewire `/livewire-{hash}/update` POST scoped to
//                    the results island (wire:island="results", `name`
//                    update + `$commit` call) -- see
//                    tests/Feature/TorrentSearchIslandsTest.php
//   1/2 stats      - real Livewire update POST calling
//                    `refreshDisplayedStats` (no updates, no HTML/island
//                    render, dispatches `torrent-stats-refreshed`)
// No mocked handlers, no substitute JSON endpoint standing in for the
// Livewire protocol.
import http from 'k6/http';
import { check } from 'k6';
import { SharedArray } from 'k6/data';
import { Trend, Counter } from 'k6/metrics';
import { loadManifest } from './lib/manifest.js';
import { login } from './lib/auth.js';
import { extractLivewireConfig, extractSnapshot, livewireUpdate } from './lib/livewire.js';
import { createPhaseMetrics } from './lib/metrics.js';

const BASE_URL = __ENV.CAPACITY_BASE_URL_HTTPS || 'https://capacity-nginx';
const WEB_RATE = parseInt(__ENV.WEB_RATE || '60', 10);
const DURATION = __ENV.WEB_DURATION || '5m';
// Each VU logs in once (real Fortify + bcrypt). Letting k6 grow the pool
// freely turns a slow run into a login storm that measures password hashing
// instead of the page under test, so the logged-in population is bounded to
// the concurrency the configured rate actually needs.
// A member is throttled to 30 requests/minute (GlobalRateLimit::WEB), so a
// 60/s aggregate rate needs at least 120 distinct members; 15/min each keeps
// the steady state clear of that ceiling instead of measuring the throttle.
const INITIAL_WEB_USERS = Math.max(8, Math.ceil((WEB_RATE * 60) / 15));
const MAX_LOGGED_IN_USERS = parseInt(__ENV.MAX_WEB_USERS || String(INITIAL_WEB_USERS), 10);
const COMPONENT_NAME = 'torrent-search';

// SharedArray parses its init callback's return value ONCE GLOBALLY and
// shares the same read-only copy across every VU. EVERY manifest field read
// here -- including tiny scalars, not just the usernames array -- MUST go
// through SharedArray: a plain top-level `const x = loadManifest().field`
// still re-opens and re-JSON.parses the ENTIRE manifest.json (all 40000
// peer records, even though this file never uses them) once per VU, because
// k6 re-runs top-level script code per VU except for whatever SharedArray
// itself caches. That exact mistake -- `web_password` previously read as a
// bare `loadManifest().web_password` -- OOM'd a 129-VU/<1GiB smoke run.
const usernames = new SharedArray('web_usernames', () => loadManifest().usernames);
const webPassword = new SharedArray('web_password', () => [loadManifest().web_password])[0];

export const webAuthInitLatency = new Trend('web_auth_init_ms', true);
export const loginFailures = new Counter('web_login_failures');
export const searchIslandFailures = new Counter('web_search_island_failures');
export const statsDispatchFailures = new Counter('web_stats_dispatch_failures');
const webMetrics = createPhaseMetrics('web');

export const options = {
  scenarios: {
    web_roundtrips: {
      executor: 'constant-arrival-rate',
      rate: WEB_RATE,
      timeUnit: '1s',
      duration: DURATION,
      preAllocatedVUs: INITIAL_WEB_USERS,
      maxVUs: MAX_LOGGED_IN_USERS,
      exec: 'webRoundtrip',
    },
  },
};

let jar = null;
let clientHeaders = null;
let authedUsername = null;
let updateUri = null;
let csrfToken = null;
let snapshot = null;

function initVu() {
  const initStart = Date.now();

  jar = http.cookieJar();
  clientHeaders = { 'X-Forwarded-For': `10.71.${(__VU >> 8) & 255}.${__VU & 255}` };
  const idx = __VU % usernames.length;
  authedUsername = usernames[idx];
  const loggedIn = login(jar, authedUsername, webPassword, clientHeaders);
  if (!loggedIn) {
    loginFailures.add(1);
  }

  const catalogueRes = http.get(`${BASE_URL}/torrents`, { jar, headers: clientHeaders, tags: { name: 'auth_init_catalogue' } });
  check(catalogueRes, { 'auth-init catalogue status 200': (r) => r.status === 200 });

  const config = extractLivewireConfig(catalogueRes.body);
  updateUri = config.updateUri;
  csrfToken = config.csrfToken;
  snapshot = extractSnapshot(catalogueRes.body, COMPONENT_NAME);

  webAuthInitLatency.add(Date.now() - initStart);
}

function doCatalogue() {
  const res = http.get(`${BASE_URL}/torrents`, { jar, headers: clientHeaders, tags: { name: 'catalogue' } });
  const completedAt = Date.now();

  const ok = check(res, { 'catalogue status 200': (r) => r.status === 200 });
  if (!ok) {
    webMetrics.record(res.timings.duration, true, completedAt);
    return;
  }

  const config = extractLivewireConfig(res.body);
  updateUri = config.updateUri;
  csrfToken = config.csrfToken;
  snapshot = extractSnapshot(res.body, COMPONENT_NAME);
  webMetrics.record(res.timings.duration, false, completedAt);
}

function doSearch() {
  const name = `Capacity Benchmark Torrent ${1 + (exec_iter() % 10000)}`;

  const { res, json } = livewireUpdate(
    jar,
    updateUri,
    csrfToken,
    snapshot,
    {
      updates: { name },
      calls: [
        {
          method: '$commit',
          params: [],
          path: '',
          metadata: { island: { name: 'results', mode: 'morph' } },
        },
      ],
    },
    { name: 'search' },
    clientHeaders
  );
  const completedAt = Date.now();

  const effects = json && json.components && json.components[0] && json.components[0].effects;
  const ok = check(res, {
    'search status 200': (r) => r.status === 200,
    'search response has island fragments, no full html': () => !!effects && 'islandFragments' in effects && !('html' in effects),
  });

  webMetrics.record(res.timings.duration, !ok, completedAt);

  if (!ok) {
    searchIslandFailures.add(1);
    return;
  }

  // Per Livewire's own update contract: the response's snapshot is the new
  // authoritative state for the next call.
  snapshot = json.components[0].snapshot;
}

function doStats() {
  const { res, json } = livewireUpdate(
    jar,
    updateUri,
    csrfToken,
    snapshot,
    {
      updates: {},
      calls: [
        {
          method: 'refreshDisplayedStats',
          params: [],
          path: '',
          metadata: {},
        },
      ],
    },
    { name: 'stats' },
    clientHeaders
  );
  const completedAt = Date.now();

  const effects = json && json.components && json.components[0] && json.components[0].effects;
  const dispatched = effects && Array.isArray(effects.dispatches) && effects.dispatches.some((d) => d.name === 'torrent-stats-refreshed');
  const ok = check(res, {
    'stats status 200': (r) => r.status === 200,
    'stats response dispatches torrent-stats-refreshed, no html/islands': () =>
      !!effects && !('html' in effects) && !('islandFragments' in effects) && dispatched,
  });

  webMetrics.record(res.timings.duration, !ok, completedAt);

  if (!ok) {
    statsDispatchFailures.add(1);
    return;
  }

  snapshot = json.components[0].snapshot;
}

// Deterministic-enough per-iteration counter (no cross-VU shared state
// needed here, unlike the announce swarm's peer selection).
let localIterCount = 0;
function exec_iter() {
  return localIterCount++;
}

export function webRoundtrip() {
  const startedAt = Date.now();
  try {
    if (!jar) initVu();
    const r = Math.random();
    if (r < 1 / 3) doCatalogue();
    else if (r < 1 / 3 + 1 / 6) doSearch();
    else doStats();
  } catch (error) {
    webMetrics.record(Date.now() - startedAt, true);
    throw error;
  }
}
