// Real tracker-protocol load: every request is a genuine GET against the
// isolated stack's /announce/{passkey} endpoint with real query parameters
// (no mocked handler), announcing AS one of `capacity:seed-fixtures`'s real,
// already-existing peer rows (real passkey/info_hash/peer_id and seeded
// uploaded/downloaded/left baseline) -- never a synthetic identity layered
// on top of them. A bounded VU pool (never per-peer VUs) drives the whole
// peer pool by picking a different real peer on every iteration from the
// scenario's global, monotonically increasing iteration counter -- so the
// aggregate rate matches ANNOUNCE_RATE while each individual peer naturally
// re-announces roughly once every (PEER_POOL / ANNOUNCE_RATE) seconds,
// matching the server's configured per-peer min announce interval
// (.env.capacity ANNOUNCE_INTERVAL_MIN/MAX) without any manual per-iteration
// sleep. One dedicated, separately-driven canary peer (excluded from this
// pool) provides the deterministic series used by `capacity:verify-canary`.
import http from 'k6/http';
import { check, sleep } from 'k6';
import { SharedArray } from 'k6/data';
import exec from 'k6/execution';
import { Counter, Gauge } from 'k6/metrics';
import { loadManifest, hexToPercentEncoded } from './lib/manifest.js';
import { bdecode } from './lib/bencode.js';
import { createPhaseMetrics } from './lib/metrics.js';
import { elapsedSecondsSinceBenchStart } from './lib/phase.js';

const BASE_URL = __ENV.CAPACITY_BASE_URL || 'http://capacity-nginx';
const ANNOUNCE_RATE = parseInt(__ENV.ANNOUNCE_RATE || '121', 10);
const DURATION = __ENV.ANNOUNCE_DURATION || '5m';
const CANARY_UPLOAD_STEP = 1048576; // 1 MiB raw-reported increase per canary announce
const CANARY_DOWNLOAD_STEP = 524288; // 512 KiB

// SharedArray parses its init callback's return value ONCE GLOBALLY and
// shares the same read-only copy across every VU. Every manifest field this
// file reads -- including tiny scalars, not just arrays -- MUST go through
// SharedArray: a plain top-level `const x = loadManifest().field` still
// re-opens and re-JSON.parses the ENTIRE manifest.json (all 40000 peer
// records) once per VU, because k6 re-runs top-level script code per VU
// except for whatever SharedArray itself caches. That exact mistake (an
// unwrapped scalar read elsewhere in this package) OOM'd a 129-VU/<1GiB
// smoke run; every read here is wrapped so it can never regress.
const peers = new SharedArray('peers', () => loadManifest().peers);
const canary = new SharedArray('canary', () => [loadManifest().canary])[0];

if (peers.length === 0) {
  throw new Error('Manifest has no `peers` -- re-run capacity:seed-fixtures (older manifests predate this field).');
}

// Pool size is exactly the real fixture peer count, not a rate-derived
// guess: re-announcing a real peer before the server's configured
// ANNOUNCE_INTERVAL_MIN has elapsed is a genuine rejection, not something to
// paper over by inventing more identities than actually exist. Scenario
// duration must be chosen (by the orchestrator) so `peers.length /
// ANNOUNCE_RATE` is either >= the scenario duration (no peer repeats at
// all) or >= ANNOUNCE_INTERVAL_MIN (repeats are spaced out enough to be
// legal).
const PEER_POOL = peers.length;

const announceMetrics = createPhaseMetrics('announce');
export const canaryAnnounces = new Counter('canary_announces');
export const canaryRejections = new Counter('canary_rejections');
// `handleSummary()` executes in its own fresh VU context, separate from the
// single VU that actually ran `canaryAnnounce()` across the whole test --
// plain module-scope `let` variables mutated during iterations are NOT
// visible there. Gauges (last `.add()` value wins) are k6's own mechanism
// for carrying final per-run state into `data.metrics.<name>.values.value`
// inside handleSummary, so that is what the canary's expected totals are
// surfaced through instead.
export const canaryExpectedCursorUploaded = new Gauge('canary_expected_cursor_uploaded');
export const canaryExpectedCursorDownloaded = new Gauge('canary_expected_cursor_downloaded');
export const canaryExpectedCursorLeft = new Gauge('canary_expected_cursor_left');
export const canaryExpectedCreditedUploadedGauge = new Gauge('canary_expected_credited_uploaded');
export const canaryExpectedCreditedDownloadedGauge = new Gauge('canary_expected_credited_downloaded');

export const options = {
  scenarios: {
    announce_swarm: {
      executor: 'constant-arrival-rate',
      rate: ANNOUNCE_RATE,
      timeUnit: '1s',
      duration: DURATION,
      // Bounded regardless of PEER_POOL/rate: the executor only needs enough
      // concurrent VUs to cover in-flight request latency, not one per
      // logical peer.
      preAllocatedVUs: 64,
      maxVUs: 256,
      exec: 'swarmAnnounce',
    },
    canary: {
      executor: 'constant-vus',
      vus: 1,
      duration: DURATION,
      exec: 'canaryAnnounce',
    },
  },
};

function buildAnnounceUrl(passkey, infoHashHex, peerIdHex, uploaded, downloaded, left, event) {
  const params =
    `info_hash=${hexToPercentEncoded(infoHashHex)}` +
    `&peer_id=${hexToPercentEncoded(peerIdHex)}` +
    `&port=51413` +
    `&uploaded=${uploaded}` +
    `&downloaded=${downloaded}` +
    `&left=${left}` +
    `&numwant=25` +
    `&corrupt=0` +
    `&key=${peerIdHex.substr(0, 8)}` +
    (event ? `&event=${event}` : '');

  return `${BASE_URL}/announce/${passkey}?${params}`;
}

function ipFor(index) {
  const a = (index >> 8) & 0xff;
  const b = index & 0xff;
  return `10.70.${a}.${b}`;
}

// Decodes a raw announce response body and returns
// `{ ok: boolean, isFailure: boolean, decoded }`. `ok` is false for a
// response that is not even well-formed bencode (transport/server error);
// `isFailure` is true for a well-formed bencode dict that is nonetheless a
// logical tracker rejection (`failure reason`) -- which the server returns
// with HTTP 200, so status alone can never distinguish it.
function parseAnnounceResponse(body) {
  if (!(body instanceof ArrayBuffer)) return { ok: false, isFailure: false, decoded: null };
  const bytes = new Uint8Array(body);
  if (bytes[0] !== 0x64 || bytes[bytes.length - 1] !== 0x65) {
    return { ok: false, isFailure: false, decoded: null };
  }

  let decoded;
  try {
    const result = bdecode(bytes);
    if (result.consumed !== bytes.length) return { ok: false, isFailure: false, decoded: null };
    decoded = result.value;
  } catch (e) {
    return { ok: false, isFailure: false, decoded: null };
  }

  return { ok: true, isFailure: 'failure reason' in decoded, decoded };
}

const UPLOAD_RATE_BYTES_PER_S = 65536;
const DOWNLOAD_RATE_BYTES_PER_S = 32768;

export function swarmAnnounce() {
  // Global, monotonically increasing across the whole scenario regardless
  // of which VU picks it up -- this is what makes the *same* real fixture
  // peer (not VU) recur roughly every PEER_POOL/ANNOUNCE_RATE seconds.
  const globalIter = exec.scenario.iterationInTest;
  const peerSlot = globalIter % PEER_POOL;
  const peer = peers[peerSlot];

  // Rising counters tied to elapsed wall-clock time SINCE THE SCENARIO
  // STARTED (a single, globally-shared instant via BENCH_START_MS -- not
  // `Date.now()` captured separately per VU at that VU's own init time,
  // which would give a different, inconsistent baseline to whichever VU
  // happens to revisit this exact peer later) ON TOP OF this peer's real
  // seeded baseline -- so the very first announce for it during this run
  // already computes a genuine, non-zero credited delta against its true
  // starting point, not a from-zero synthetic series.
  const elapsedSeconds = Math.max(1, Math.floor(elapsedSecondsSinceBenchStart()));
  const uploaded = peer.uploaded + elapsedSeconds * UPLOAD_RATE_BYTES_PER_S;
  const downloaded = peer.left > 0 ? peer.downloaded + elapsedSeconds * DOWNLOAD_RATE_BYTES_PER_S : peer.downloaded;
  const left = Math.max(0, peer.left - elapsedSeconds * DOWNLOAD_RATE_BYTES_PER_S);

  const url = buildAnnounceUrl(peer.passkey, peer.info_hash, peer.peer_id, uploaded, downloaded, left, '');

  const res = http.get(url, {
    headers: {
      'User-Agent': 'qBittorrent/4.6.0',
      'X-Forwarded-For': ipFor(peerSlot),
    },
    timeout: '8s',
    responseType: 'binary',
    tags: { name: 'announce_swarm' },
  });
  const completedAt = Date.now();

  const { ok, isFailure } = parseAnnounceResponse(res.body);

  // A bencoded `failure reason` is a genuine tracker-level rejection even
  // though the HTTP status is 200 -- it counts as a failure for phase
  // metrics/thresholds exactly like a transport-level failure, never
  // silently excluded just because the status check passed.
  const httpOk = check(res, { 'announce status 200': (r) => r.status === 200 });
  const failed = !httpOk || !ok || isFailure;

  announceMetrics.record(res.timings.duration, failed, completedAt);
}

let canaryCount = 0;
// `ProcessAnnounce` forces a brand-new peer's first announce to a zero-delta
// `started` event regardless of the uploaded/downloaded it reports, so the
// *credited* total (what lands in `users.uploaded`/`users.downloaded`) is
// the sum of deltas from the SECOND announce onward, not from the first.
// Track that explicitly rather than assuming a closed-form formula.
let canaryPreviousUploaded = 0;
let canaryPreviousDownloaded = 0;
let canaryExpectedCreditedUploaded = 0;
let canaryExpectedCreditedDownloaded = 0;
let canaryLastRawTotals = { uploaded: 0, downloaded: 0, left: 1073741824 };

export function canaryAnnounce() {
  const passkey = canary.passkey;
  const infoHashHex = canary.info_hash;
  const peerIdHex = canary.peer_id;

  // `canaryCount` only ever advances on a CONFIRMED-accepted announce (see
  // below): a truthful "accepted" series, not an attempt counter. If the
  // previous attempt was rejected, this retries the exact same candidate
  // uploaded/downloaded/left rather than silently skipping ahead as though
  // the server had applied credit it never actually applied.
  const attemptNumber = canaryCount + 1;
  const uploaded = attemptNumber * CANARY_UPLOAD_STEP;
  const downloaded = attemptNumber * CANARY_DOWNLOAD_STEP;
  const left = Math.max(0, 1073741824 - downloaded);

  const url = buildAnnounceUrl(passkey, infoHashHex, peerIdHex, uploaded, downloaded, left, attemptNumber === 1 ? 'started' : '');

  const res = http.get(url, {
    headers: {
      'User-Agent': 'qBittorrent/4.6.0',
      'X-Forwarded-For': '10.70.255.1',
    },
    timeout: '8s',
    responseType: 'binary',
    tags: { name: 'canary' },
  });

  const httpOk = check(res, { 'canary announce status 200': (r) => r.status === 200 });
  const { ok, isFailure, decoded } = parseAnnounceResponse(res.body);
  const accepted = httpOk && ok && !isFailure && decoded !== null;

  check(res, { 'canary announce accepted (valid bencode, no failure reason)': () => accepted });

  if (accepted) {
    canaryCount = attemptNumber;

    if (canaryCount === 1) {
      // First announce for a never-before-seen peer: server-side delta is
      // forced to zero (event forced to `started`) no matter what we send.
      canaryPreviousUploaded = uploaded;
      canaryPreviousDownloaded = downloaded;
    } else {
      canaryExpectedCreditedUploaded += uploaded - canaryPreviousUploaded;
      canaryExpectedCreditedDownloaded += downloaded - canaryPreviousDownloaded;
      canaryPreviousUploaded = uploaded;
      canaryPreviousDownloaded = downloaded;
    }

    canaryLastRawTotals = { uploaded, downloaded, left };

    canaryExpectedCursorUploaded.add(uploaded);
    canaryExpectedCursorDownloaded.add(downloaded);
    canaryExpectedCursorLeft.add(left);
    canaryExpectedCreditedUploadedGauge.add(canaryExpectedCreditedUploaded);
    canaryExpectedCreditedDownloadedGauge.add(canaryExpectedCreditedDownloaded);

    canaryAnnounces.add(1);
  } else {
    canaryRejections.add(1);
  }

  // The canary is the one place a manual sleep is correct: it is a single
  // dedicated VU (negligible memory) that must respect the server's min
  // announce interval so its own announces are never rejected.
  const sleepSeconds = parseInt(__ENV.CANARY_INTERVAL_SECONDS || '320', 10);
  sleep(sleepSeconds);
}

function gaugeValue(data, name, fallback) {
  const m = data.metrics && data.metrics[name];
  return m && m.values && typeof m.values.value === 'number' ? m.values.value : fallback;
}

export function handleSummary(data) {
  // `data` is the one thing handleSummary genuinely receives from the real
  // test run (k6 aggregates every metric across every VU/context into it),
  // so every canary field is read back from it rather than from the local
  // closure variables above, which do not survive into this context.
  const announceCount = (data.metrics && data.metrics.canary_announces && data.metrics.canary_announces.values.count) || 0;
  const rejectionCount = (data.metrics && data.metrics.canary_rejections && data.metrics.canary_rejections.values.count) || 0;

  const expected = {
    user_id: canary.user_id,
    torrent_id: canary.torrent_id,
    peer_id_hex: canary.peer_id,
    expected_cursor_uploaded: gaugeValue(data, 'canary_expected_cursor_uploaded', 0),
    expected_cursor_downloaded: gaugeValue(data, 'canary_expected_cursor_downloaded', 0),
    expected_cursor_left: gaugeValue(data, 'canary_expected_cursor_left', 1073741824),
    expected_credited_uploaded: gaugeValue(data, 'canary_expected_credited_uploaded', 0),
    expected_credited_downloaded: gaugeValue(data, 'canary_expected_credited_downloaded', 0),
    announce_count: announceCount,
    rejected_attempts: rejectionCount,
  };

  return {
    '/results/canary-expected.json': JSON.stringify(expected, null, 2),
    '/results/announce-summary.json': JSON.stringify(data, null, 2),
    stdout: JSON.stringify({ requested_rate: ANNOUNCE_RATE, canary_announces: announceCount, canary_rejections: rejectionCount, canary_expected: expected }),
  };
}
