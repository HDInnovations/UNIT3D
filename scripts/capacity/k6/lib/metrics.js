import { Trend, Counter } from 'k6/metrics';
import { classifyPhase, PHASES } from './phase.js';

/**
 * One Trend + one Counter PER PHASE for a traffic category ("announce" or
 * "web"), e.g. `announce_latency_steady_ms`, `announce_failures_db_outage`.
 * Metric names must be static/known at init time in k6, so this predeclares
 * the full fixed set (4 phases) rather than creating metrics dynamically
 * mid-test.
 *
 * `record()` classifies by RESPONSE-COMPLETION time (the caller should call
 * it right after the request returns, passing that moment), per the
 * requirement that a request whose interval crosses into a fault window
 * must count against that fault, not steady.
 */
export function createPhaseMetrics(category) {
  const latency = {};
  const failures = {};
  const requests = {};

  for (const phase of PHASES) {
    latency[phase] = new Trend(`${category}_latency_${phase}_ms`, true);
    failures[phase] = new Counter(`${category}_failures_${phase}`);
    requests[phase] = new Counter(`${category}_requests_${phase}`);
  }

  return {
    record(durationMs, failed, nowMs = Date.now()) {
      const phase = classifyPhase(nowMs, durationMs);
      latency[phase].add(durationMs);
      requests[phase].add(1);
      failures[phase].add(failed ? 1 : 0);
      return phase;
    },
  };
}
