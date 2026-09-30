#!/usr/bin/env bash
set -euo pipefail

project_root=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)
readonly project_root
readonly pve_host='root@100.108.105.119'
readonly pve_path='/srv/storage/backup/proxmox/vltava'

cd "$project_root"

docker compose exec -T -u "$(id -u):$(id -g)" laravel.test php artisan backup:run --only-db
docker compose exec -T -u "$(id -u):$(id -g)" laravel.test php artisan backup:run --only-files
docker compose exec -T -u "$(id -u):$(id -g)" laravel.test php artisan backup:clean

ssh -o BatchMode=yes "$pve_host" "install -d -m 0700 '$pve_path'"
rsync -a --protect-args --chmod=F600,D700 storage/backups/ "$pve_host:$pve_path/"

docker compose exec -T laravel.test php artisan backup:monitor
