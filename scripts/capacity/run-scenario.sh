#!/usr/bin/env bash
# Runs one capacity-benchmark scenario end-to-end against the isolated
# `capacity` stack: starts k6, polls `capacity:status` throughout, optionally
# injects a tracker-worker restart and a brief MySQL pause, waits for k6 to
# finish, drains the tracker queue/batches, verifies the canary peer
# lost/double-counted nothing, and writes a single combined machine-readable
# report.
#
# Scenarios (all bounded, host-resource-capped, open-loop):
#   baseline  - 121 announces/s + 60 web/s for DURATION, no chaos
#   elevated  - ANNOUNCE_RATE/WEB_RATE above baseline, no chaos
#   burst     - short high-rate spike
#   sustained - baseline rates for a long DURATION (default 15m) plus a
#               tracker-worker restart and a brief (<=5s) MySQL pause; the
#               chaos window itself must actually happen (chaos.sh fails
#               loudly if it can't), its own errors are reported separately
#               from the steady-state phases, not blended into a false pass
#
# Usage: scripts/capacity/run-scenario.sh <baseline|elevated|burst|sustained> [duration-seconds]
#
# Realistic run durations: baseline/elevated 5m, burst 1m, sustained >=15m.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

COMPOSE=(docker compose -p capacity -f docker-compose.capacity.yml)
SCENARIO="${1:?scenario required: baseline|elevated|burst|sustained}"
RESULTS_DIR="scripts/capacity/results"
RUN_ID="$(date -u +%Y%m%dT%H%M%SZ)-${SCENARIO}"
RUN_DIR="${RESULTS_DIR}/${RUN_ID}"
mkdir -p "$RUN_DIR"

# Optional 2nd arg overrides duration in whole seconds (e.g. `900`), never a
# k6 duration string — kept as one integer so it can drive both k6's
# `--duration` flag and the bash-side chaos/status-collector timers.
# The announce swarm's peer pool is EXACTLY the real fixture peer count
# seeded by `capacity:seed-fixtures` (default 40000 -- k6 reads
# `manifest.peers.length` itself, there is no rate-derived override here
# anymore: synthesizing more peer identities than actually exist would mean
# never exercising their real seeded baseline). Each scenario's duration is
# therefore chosen so (peer pool / ANNOUNCE_RATE) -- the average gap before
# any one real peer repeats -- is either >= the scenario duration (no peer
# ever repeats) or >= ANNOUNCE_INTERVAL_MIN (.env.capacity, default 300s;
# repeats are legal, spaced out enough):
#   baseline  121/s: 40000/121 ~= 331s > 300s duration -> zero repeats
#   elevated  242/s: 40000/242 ~= 165s -> duration capped at 150s -> zero repeats
#   burst     600/s: 40000/600 ~=  67s -> duration capped at  60s -> zero repeats
#   sustained 121/s: 40000/121 ~= 331s < 900s duration -> repeats every
#                    ~331s, which is > the 300s min interval -> legal
case "$SCENARIO" in
    baseline)
        ANNOUNCE_RATE=121; WEB_RATE=60; DURATION_S="${2:-300}"; CHAOS=0 ;;
    elevated)
        ANNOUNCE_RATE=242; WEB_RATE=120; DURATION_S="${2:-150}"; CHAOS=0 ;;
    burst)
        ANNOUNCE_RATE=600; WEB_RATE=300; DURATION_S="${2:-60}"; CHAOS=0 ;;
    sustained)
        ANNOUNCE_RATE=121; WEB_RATE=60; DURATION_S="${2:-900}"; CHAOS=1 ;;
    *)
        echo "Unknown scenario: $SCENARIO (expected baseline|elevated|burst|sustained)" >&2
        exit 1 ;;
esac
DURATION="${DURATION_S}s"

echo "==> Scenario ${SCENARIO}: announce=${ANNOUNCE_RATE}/s web=${WEB_RATE}/s duration=${DURATION} chaos=${CHAOS} (peer pool size comes from the seeded manifest, not a flag)"

if [ ! -f "${RESULTS_DIR}/manifest.json" ]; then
    echo "${RESULTS_DIR}/manifest.json missing; run scripts/capacity/bootstrap.sh first" >&2
    exit 1
fi

# One wall-clock epoch drives both the actual faults and k6 phase labels.
BENCH_START_MS=$(date +%s%3N)
WORKER_RESTART_AT=$(( DURATION_S / 3 ))
DB_PAUSE_AT=$(( DURATION_S * 2 / 3 ))

# --- Background status collector ------------------------------------------
STATUS_FILE="${RUN_DIR}/status-timeline.ndjson"
scripts/capacity/collect-status.sh "$STATUS_FILE" "$((DURATION_S + 60))" 5 &
STATUS_PID=$!

# --- Optional chaos (sustained scenario only) ------------------------------
CHAOS_PID=""
if [ "$CHAOS" = "1" ]; then
    BENCH_START_MS="$BENCH_START_MS" scripts/capacity/chaos.sh "$WORKER_RESTART_AT" "$DB_PAUSE_AT" 5 > "${RUN_DIR}/chaos.log" 2>&1 &
    CHAOS_PID=$!
fi

cleanup() {
    [ -n "$CHAOS_PID" ] && kill "$CHAOS_PID" 2>/dev/null || true
    kill "$STATUS_PID" 2>/dev/null || true
}
trap cleanup EXIT INT TERM

# --- k6 (foreground; this is the thing we actually wait on) ----------------
K6_EXIT=0
"${COMPOSE[@]}" run --rm \
    -e ANNOUNCE_RATE="$ANNOUNCE_RATE" \
    -e ANNOUNCE_DURATION="$DURATION" \
    -e WEB_RATE="$WEB_RATE" \
    -e WEB_DURATION="$DURATION" \
    -e BENCH_START_MS="$BENCH_START_MS" \
    -e CHAOS_ENABLED="$CHAOS" \
    -e CHAOS_WORKER_RESTART_AT="$WORKER_RESTART_AT" \
    -e CHAOS_DB_PAUSE_AT="$DB_PAUSE_AT" \
    -e CHAOS_DB_PAUSE_DURATION=5 \
    capacity-k6 run /scripts/scenario.js \
    --summary-export=/results/k6-summary.json \
    || K6_EXIT=$?

CHAOS_EXIT=0
if [ -n "$CHAOS_PID" ]; then
    wait "$CHAOS_PID" || CHAOS_EXIT=$?
fi
kill "$STATUS_PID" 2>/dev/null || true
wait "$STATUS_PID" 2>/dev/null || true
trap - EXIT INT TERM

# --- Drain: wait for the tracker queue/batches to empty (bounded) before
# verifying the canary, so "canary row still has a pending outbox entry" is
# a real finding, not just "we checked before delivery finished". ----------
DRAIN_TIMEOUT_S=180
drained=0
for _ in $(seq 1 "$DRAIN_TIMEOUT_S"); do
    status_json=$("${COMPOSE[@]}" exec -T -u sail capacity-app php artisan capacity:status --json 2>/dev/null || echo '{}')
    is_drained=$("${COMPOSE[@]}" exec -T -u sail capacity-app php -r '
        $s = json_decode(trim(fgets(STDIN) ?: "{}"), true) ?: [];
        $q = $s["queues"]["tracker"] ?? [];
        $pending = (int) ($q["pending"] ?? 1);
        $delayed = (int) ($q["delayed"] ?? 0);
        $reserved = (int) ($q["reserved"] ?? 0);
        $batches = $s["batches"] ?? [];
        $batchTotal = array_sum(array_map("intval", $batches));
        echo ($pending + $delayed + $reserved + $batchTotal) === 0 ? "1" : "0";
    ' <<< "$status_json" 2>/dev/null || echo "0")
    if [ "$is_drained" = "1" ]; then
        drained=1
        break
    fi
    sleep 1
done
if [ "$drained" != "1" ]; then
    echo "Warning: tracker queue did not report fully drained within ${DRAIN_TIMEOUT_S}s; verifying anyway (a non-empty pending outbox will surface as a real failure)." | tee "${RUN_DIR}/drain.log"
else
    echo "Tracker queue drained after polling capacity:status." | tee "${RUN_DIR}/drain.log"
fi

# --- Canary verification ---------------------------------------------------
CANARY_EXIT=0
if [ -f "${RESULTS_DIR}/canary-expected.json" ]; then
    "${COMPOSE[@]}" exec -T -u sail capacity-app php artisan capacity:verify-canary /var/www/capacity-results/canary-expected.json \
        | tee "${RUN_DIR}/canary-verification.log" || CANARY_EXIT=$?
else
    echo "canary-expected.json not written by k6; skipping canary verification" | tee "${RUN_DIR}/canary-verification.log"
    CANARY_EXIT=1
fi

# --- Move per-run k6/status artifacts into the run directory ---------------
mv "${RESULTS_DIR}/k6-summary.json" "${RUN_DIR}/" 2>/dev/null || true
mv "${RESULTS_DIR}/announce-summary.json" "${RUN_DIR}/" 2>/dev/null || true
mv "${RESULTS_DIR}/canary-expected.json" "${RUN_DIR}/" 2>/dev/null || true

cat > "${RUN_DIR}/report.json" <<JSON
{
  "scenario": "${SCENARIO}",
  "announce_rate_per_s": ${ANNOUNCE_RATE},
  "web_rate_per_s": ${WEB_RATE},
  "duration_seconds": ${DURATION_S},
  "chaos_injected": $([ "$CHAOS" = "1" ] && echo true || echo false),
  "k6_exit_code": ${K6_EXIT},
  "chaos_exit_code": ${CHAOS_EXIT},
  "canary_exit_code": ${CANARY_EXIT},
  "queues_and_batches_drained": $([ "$drained" = "1" ] && echo true || echo false),
  "measurement_start_unix_ms": ${BENCH_START_MS},
  "resource_caps": {"total_cpus": 26.625, "total_mem_gib": 27, "note": "shared host, not dedicated hardware; see docker-compose.capacity.yml per-service cpus/mem_limit"},
  "measurement_phases": ["steady", "worker_restart", "db_outage", "recovery"],
  "artifacts": {
    "k6_summary": "k6-summary.json",
    "status_timeline": "status-timeline.ndjson",
    "canary_verification": "canary-verification.log",
    "chaos_log": "chaos.log",
    "drain_log": "drain.log"
  }
}
JSON

echo "==> Report: ${RUN_DIR}/report.json"

if [ "$K6_EXIT" != "0" ] || [ "$CANARY_EXIT" != "0" ] || [ "$CHAOS_EXIT" != "0" ] || [ "$drained" != "1" ]; then
    exit 1
fi
