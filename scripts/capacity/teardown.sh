#!/usr/bin/env bash
# Tears down the isolated `capacity` stack and its disposable volumes.
# Never touches the live `vltava` compose project.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

docker compose -p capacity -f docker-compose.capacity.yml down -v --remove-orphans
