#!/usr/bin/env bash
# Polls `php artisan capacity:status --json` every INTERVAL seconds for the
# duration of a scenario and appends each snapshot (one JSON object per
# line) to an NDJSON file, so queue age, batch depths, FPM saturation and
# command timings are observable over the whole run, not just at the end.
#
# Usage: scripts/capacity/collect-status.sh <output-file> <total-duration-seconds> [interval-seconds]
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

COMPOSE=(docker compose -p capacity -f docker-compose.capacity.yml)
OUT="${1:?output file required}"
DURATION="${2:?duration seconds required}"
INTERVAL="${3:-5}"

: > "$OUT"
END=$(( $(date +%s) + DURATION ))

while [ "$(date +%s)" -lt "$END" ]; do
    exit_code=0
    status_json=$("${COMPOSE[@]}" exec -T -u sail capacity-app php artisan capacity:status --json 2>/dev/null) || exit_code=$?
    status_json=$(printf '%s' "$status_json" | jq -ces 'if length == 1 and (.[0] | type) == "object" then .[0] else error("invalid status") end' 2>/dev/null) \
        || status_json='{"error":"capacity:status returned no valid JSON object"}'
    printf '{"polled_at":"%s","command_exit_code":%s,"status":%s}\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$exit_code" "$status_json" >> "$OUT"
    sleep "$INTERVAL"
done
