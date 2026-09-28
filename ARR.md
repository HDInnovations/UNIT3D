# Local ARR stack

Start UNIT3D, the Torznab adapter, Prowlarr, Sonarr, Radarr, and qBittorrent:

```sh
docker compose -f docker-compose.yml -f docker-compose.arr.yml up -d
```

Local interfaces are loopback-only:

- UNIT3D: `https://localhost:8444`
- Prowlarr: `http://localhost:9696`
- Sonarr: `http://localhost:8989`
- Radarr: `http://localhost:7878`
- qBittorrent: `http://localhost:8085`

`torznab-adapter` translates UNIT3D's authenticated JSON API into Torznab caps, movie search, TV search, metadata IDs, and categories. Its `apikey` is the caller's own UNIT3D API token: it never uses a shared tracker-owner credential. Search results and download links therefore retain the caller's UNIT3D permissions and passkey.

The HTTPS route is `/torznab/api`; locally it is `https://localhost:8444/torznab/api` and uses a self-signed certificate. For external users, publish it as `https://<tracker-host>/torznab/api` with a trusted certificate. The adapter has no host-published port; nginx proxies this route without access logging the token query string.

Each user adds it as **Generic Torznab** in Prowlarr with base URL `https://<tracker-host>/torznab`, API path `/api`, and their own UNIT3D API token in the API-key field. The same endpoint can be added directly as Torznab in Sonarr or Radarr. Prowlarr contains one enabled local example indexer, `UNIT3D local`, and syncs Movies to Radarr and TV to Sonarr. Do not add UNIT3D directly in Prowlarr.

All download clients mount the same project-local paths. No remote path mappings are necessary:

| Application | qBittorrent category | Download path | Library root |
| --- | --- | --- | --- |
| Sonarr | `sonarr` | `/downloads/tv` | `/tv` |
| Radarr | `radarr` | `/downloads/movies` | `/movies` |

The local qBittorrent service password remains in `.env` as `QBITTORRENT_PASSWORD`. Each external user keeps their own UNIT3D API token in their own Prowlarr, Sonarr, or Radarr configuration.
