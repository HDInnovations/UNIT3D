// Combined sustained-load driver: runs the real announce swarm + canary and
// the real authenticated web roundtrips side by side, matching how the
// actual stack is hit in production (tracker + web traffic concurrently).
// Rates/duration are fully configurable via env so the same file drives the
// baseline (121 announces/s + 60 web/s), elevated, and burst scenarios; the
// sustained scenario additionally receives CHAOS_ENABLED=1 plus
// BENCH_START_MS/CHAOS_WORKER_RESTART_AT/CHAOS_DB_PAUSE_AT/
// CHAOS_DB_PAUSE_DURATION so every request's latency/failure is classified
// by real wall-clock phase (steady/worker_restart/db_outage/recovery, see
// lib/phase.js) instead of blending chaos-window errors into one number.
import { options as announceOptions, swarmAnnounce, canaryAnnounce, handleSummary as announceSummary } from './announce.js';
import { options as webOptions, webRoundtrip } from './web.js';

export const options = {
  scenarios: {
    ...announceOptions.scenarios,
    ...webOptions.scenarios,
  },
  // Open-loop executors report unmet demand as `dropped_iterations`; fail
  // loud (non-zero exit) if the driver itself could not keep up with the
  // configured rate, so a benchmark never silently under-reports load.
  //
  // Only the STEADY phase gates pass/fail: `announce_failures_steady` and
  // `web_failures_steady` each already include every real rejection kind --
  // transport failures, a bencoded `failure reason` (HTTP 200, logically a
  // tracker rejection), a failed Livewire search/stats assertion. The
  // `worker_restart`/`db_outage`/`recovery` phase counterparts are
  // DELIBERATELY ungated here: errors during an intentionally-injected
  // tracker-worker restart or MySQL pause are the expected, measured
  // outcome of the sustained scenario, not a benchmark failure.
  thresholds: {
    dropped_iterations: ['count<1'],
    announce_failures_steady: ['count==0'],
    web_failures_steady: ['count==0'],
    web_login_failures: ['count==0'],
  },
};

export { swarmAnnounce, canaryAnnounce, webRoundtrip };
export const handleSummary = announceSummary;
