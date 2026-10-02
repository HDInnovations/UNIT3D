// Classifies "now" into one of four phases so the sustained scenario's
// chaos window (tracker-worker restart, brief MySQL pause) can be reported
// SEPARATELY from steady-state traffic instead of blending fault-induced
// errors/latency into a single pass/fail number.
//
// The orchestrator (owned outside this package) passes real wall-clock
// facts as env vars when it actually runs the chaos injection:
//   BENCH_START_MS            - epoch ms the scenario itself started
//   CHAOS_ENABLED              - '1' to enable phase splitting at all
//   CHAOS_WORKER_RESTART_AT    - offset seconds of the tracker-worker restart
//   CHAOS_DB_PAUSE_AT          - offset seconds the MySQL pause begins
//   CHAOS_DB_PAUSE_DURATION    - seconds the pause lasts (default 5)
// Without CHAOS_ENABLED=1 every request classifies as `steady` (baseline/
// elevated/burst scenarios, or a sustained run before the orchestrator
// supplies these facts).
const CHAOS_ENABLED = (__ENV.CHAOS_ENABLED || '0') === '1';
const BENCH_START_MS = parseInt(__ENV.BENCH_START_MS || '0', 10) || Date.now();
const WORKER_RESTART_AT_MS = parseFloat(__ENV.CHAOS_WORKER_RESTART_AT || '0') * 1000;
const DB_PAUSE_AT_MS = parseFloat(__ENV.CHAOS_DB_PAUSE_AT || '0') * 1000;
const DB_PAUSE_DURATION_MS = (parseFloat(__ENV.CHAOS_DB_PAUSE_DURATION || '5') || 5) * 1000;

// Fault windows: ±5s around the restart moment (restart itself takes ~5s),
// and the DB pause's own [start, start+duration]. 30s of `recovery` follows
// the end of either window before traffic is considered back to `steady`.
const WORKER_RESTART_HALF_WINDOW_MS = 5000;
const RECOVERY_MS = 30000;

/**
 * Classify a point in wall-clock time (default: right now). Callers should
 * pass the time the RESPONSE was received (request completion), not when
 * the request was sent, so a request that started in a steady phase but
 * whose response arrives mid-fault is correctly counted against the fault,
 * not steady -- per the orchestrator's requirement that an interval
 * crossing a fault window must count as fault.
 */
export function classifyPhase(nowMs = Date.now(), durationMs = 0) {
  if (!CHAOS_ENABLED) {
    return 'steady';
  }

  const completedAt = nowMs - BENCH_START_MS;
  const startedAt = completedAt - durationMs;
  const workerStart = WORKER_RESTART_AT_MS - WORKER_RESTART_HALF_WINDOW_MS;
  const workerEnd = WORKER_RESTART_AT_MS + WORKER_RESTART_HALF_WINDOW_MS;
  const dbStart = DB_PAUSE_AT_MS;
  const dbEnd = DB_PAUSE_AT_MS + DB_PAUSE_DURATION_MS;

  if (startedAt <= dbEnd && completedAt >= dbStart) {
    return 'db_outage';
  }
  if (startedAt <= workerEnd && completedAt >= workerStart) {
    return 'worker_restart';
  }
  if (completedAt > workerEnd && completedAt <= workerEnd + RECOVERY_MS) {
    return 'recovery';
  }
  if (completedAt > dbEnd && completedAt <= dbEnd + RECOVERY_MS) {
    return 'recovery';
  }

  return 'steady';
}

/** Elapsed seconds since the scenario itself started, globally consistent
 * across every VU (unlike a per-VU `Date.now()` captured at that VU's own
 * init time, which would differ VU-to-VU and break monotonic rising
 * counters for a peer revisited by a different VU later). */
export function elapsedSecondsSinceBenchStart(nowMs = Date.now()) {
  return Math.max(0, (nowMs - BENCH_START_MS) / 1000);
}

export const PHASES = ['steady', 'worker_restart', 'db_outage', 'recovery'];
