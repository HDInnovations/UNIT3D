#!/usr/bin/env bash
# Injects two real failure events into the isolated `capacity` stack during a
# running scenario: a tracker-worker restart and a brief (<=5s) MySQL
# pause/unpause. Operates ONLY on the `capacity` compose project; never
# touches the live stack. Always restores state via `trap`, even on error or
# signal, so a bounded outage can never linger.
#
# Usage: scripts/capacity/chaos.sh <worker-restart-at-seconds> <db-pause-at-seconds> [db-pause-duration-seconds]
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

COMPOSE=(docker compose -p capacity -f docker-compose.capacity.yml)
WORKER_RESTART_AT="${1:?worker-restart offset seconds required}"
DB_PAUSE_AT="${2:?db-pause offset seconds required}"
DB_PAUSE_SECONDS="${3:-5}"

if (( DB_PAUSE_SECONDS > 5 )); then
    echo "Refusing: db-pause-duration-seconds (${DB_PAUSE_SECONDS}) exceeds the 5s cap" >&2
    exit 1
fi

MYSQL_CID=""
cleanup() {
    if [ -n "$MYSQL_CID" ]; then
        # Idempotent: unpausing an already-running container is a no-op in Docker.
        docker unpause "$MYSQL_CID" >/dev/null 2>&1 || true
    fi
}
trap cleanup EXIT INT TERM

SCENARIO_START=$(( ${BENCH_START_MS:-$(date +%s%3N)} / 1000 ))

wait_until() {
    local target_offset="$1"
    local now elapsed remaining
    now=$(date +%s)
    elapsed=$(( now - SCENARIO_START ))
    remaining=$(( target_offset - elapsed ))
    if (( remaining > 0 )); then
        sleep "$remaining"
    fi
}

echo "==> chaos: will restart capacity-queue-tracker at t+${WORKER_RESTART_AT}s, pause MySQL for ${DB_PAUSE_SECONDS}s at t+${DB_PAUSE_AT}s"

if (( WORKER_RESTART_AT <= DB_PAUSE_AT )); then
    wait_until "$WORKER_RESTART_AT"
    echo "==> chaos: restarting capacity-queue-tracker (t+$(( $(date +%s) - SCENARIO_START ))s)"
    "${COMPOSE[@]}" restart capacity-queue-tracker

    wait_until "$DB_PAUSE_AT"
else
    wait_until "$DB_PAUSE_AT"
fi

MYSQL_CID=$("${COMPOSE[@]}" ps -q capacity-mysql)
if [ -z "$MYSQL_CID" ]; then
    echo "capacity-mysql container not found; the sustained scenario requires the chaos injection to actually happen" >&2
    exit 1
fi

echo "==> chaos: pausing capacity-mysql for ${DB_PAUSE_SECONDS}s (t+$(( $(date +%s) - SCENARIO_START ))s)"
docker pause "$MYSQL_CID"
sleep "$DB_PAUSE_SECONDS"
docker unpause "$MYSQL_CID"
MYSQL_CID=""
echo "==> chaos: capacity-mysql resumed"

if (( WORKER_RESTART_AT > DB_PAUSE_AT )); then
    wait_until "$WORKER_RESTART_AT"
    echo "==> chaos: restarting capacity-queue-tracker (t+$(( $(date +%s) - SCENARIO_START ))s)"
    "${COMPOSE[@]}" restart capacity-queue-tracker
fi

echo "==> chaos: done"
