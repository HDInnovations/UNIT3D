# Torrent API

## Introduction

UNIT3D is offering a new `{JSON:API}`. If you haven't already head over to your profile. Hover Settings nav and click API Key. From there you can generate or reset your API Key.

## Ecosystem

### Torrent auto uploaders

- [L4G's Upload Assistant](https://github.com/Audionut/Upload-Assistant) — a simple tool to take the work out of uploading
- [GG Bot Upload Assistant](https://gitlab.com/NoobMaster669/gg-bot-upload-assistant) — a torrent auto uploader to take the manual work out of uploading

## API authentication

To authenticate with UNIT3D, pass your API token as a `Bearer` token in the `Authorization` header of the request:

```php
$response = $client->request('POST', '/api/torrents', [
    'headers' => [
        'Authorization' => 'Bearer YOUR_TOKEN_HERE',
        'Accept' => 'application/json',
    ],
]);
```

## API endpoints

### Upload a torrent

Endpoint: POST `/api/torrents/upload`

Parameters:

| Parameter          | Type   | Description
|--------------------|--------|-------------
| `torrent`          | file   | .torrent file
| `nfo`              | file   | .nfo file
| `name`             | string | Torrent name
| `description`      | string | Torrent description
| `mediainfo`        | string | MediaInfo text output
| `bdinfo`           | string | BDInfo quick summary output
| `category_id`      | int    | Category ID
| `type_id`          | int    | Type ID
| `resolution_id`    | int    | Resolution ID
| `region_id`        | int    | Region ID
| `distributor_id`   | int    | Distributor ID
| `season_number`    | int    | Season number (TV only)
| `episode_number`   | int    | Episode number (TV only)
| `tmdb`             | int    | TMDB ID
| `imdb`             | int    | IMDB ID
| `tvdb`             | int    | TVDB ID
| `mal`              | int    | MAL ID
| `igdb`             | int    | IGDB ID (Games only)
| `anonymous`        | bool   | Should the uploader's username be hidden?
| `personal_release` | bool   | Is the torrent's content created by the uploader?
| `internal`*        | bool   | Is the torrent an internal release?
| `refundable`*      | bool   | Is the torrent refundable?
| `featured`*        | bool   | Should the torrent be featured on the front page?
| `free`*            | int    | Percentage (0-100) of the torrent's size that is free to leech
| `fl_until`*        | int    | Number of days the torrent should offer freeleech
| `doubleup`*        | bool   | Should the torrent offer double upload?
| `du_until`*        | int    | Number of days the torrent should offer double upload
| `sticky`*          | bool   | Should the torrent be stickied on the torrent index?
| `mod_queue_opt_in`*| bool   | Should the torrent be sent to moderation queue?

*Only available to staff and internal users.

### Fetch a torrent

Endpoint: GET `/api/torrents/:id`

Example:

```
https://unit3d.site/api/torrents/39765?api_token=YOURTOKENHERE
```

### Fetch torrents index (latest 25 torrents)

Endpoint: GET `/api/torrents`

Example:

```
https://unit3d.site/api/torrents?api_token=YOURTOKENHERE
```

### Filter torrents

Endpoint: GET `/api/torrents/filter`

Optional Parameters:

| Parameter          | Type   | Description
|--------------------|--------|-------------
| `perPage`          | int    | Amount of results to return per page (default: 25)
| `sortField`        | string | Field to sort by
| `sortDirection`    | string | Direction to sort the results. One of: `asc` (Ascending), `desc` (Descending) (default: `asc`)
| `name`             | string | Filter by the torrent's name
| `description`      | string | Filter by the torrent's description
| `mediainfo`        | string | Filter by the torrent's MediaInfo
| `bdinfo`           | string | Filter by the torrent's BDInfo
| `uploader`         | string | Filter by the torrent uploader's username
| `keywords`         | string | Filter by any of the torrent's keywords (Multiple keywords can be comma-separated)
| `startYear`        | int    | Return only torrents whose content was released after or in the given year
| `endYear`          | int    | Return only torrents whose content was released before or in the given year
| `categories`       | int[]  | Filter by the torrent's category
| `types`            | int[]  | Filter by the torrent's type
| `resolutions`      | int[]  | Filter by the torrent's resolution
| `genres`           | int[]  | Filter by the torrent's genre
| `tmdbId`           | int    | Filter by the torrent's TMDB ID
| `imdbId`           | int    | Filter by the torrent's IMDB ID
| `tvdbId`           | int    | Filter by the torrent's TVDB ID
| `malId`            | int    | Filter by the torrent's MAL ID
| `playlistId`       | int    | Return only torrents within the playlist of the given ID
| `collectionId`     | int    | Return only torrents within the collection of the given ID
| `free`             | int    | Filter by the torrent's freeleech discount (0-100)
| `doubleup`         | bool   | Filter by if the torrent offers double upload
| `featured`         | bool   | Filter by if the torrent is featured on the front page
| `refundable`       | bool   | Filter by if the torrent is refundable
| `highspeed`        | bool   | Filter by if the torrent has seeders whose IP address has been registered as a seedbox
| `internal`         | bool   | Filter by if the torrent is an internal release
| `personalRelease`  | bool   | Filter by if the torrent's content is created by the uploader
| `alive`            | bool   | Filter by if the torrent has 1 or more seeders
| `dying`            | bool   | Filter by if the torrent has 1 seeder and has been downloaded more than 3 times
| `dead`             | bool   | Filter by if the torrent has 0 seeders
| `file_name`        | string | Filter by the name of a file within a torrent
| `seasonNumber`     | int    | Filter by the torrent's season number
| `episodeNumber`    | int    | Filter by the torrent's episode number

Example:

```
https://unit3d.site/api/torrents/filter?tmdbId=475557&categories[]=1&api_token=YOURTOKENHERE
```

### Personal account info

Endpoint: GET `/api/user`

Response:
```json
{"username":"UNIT3D","group":"Owner","uploaded":"50 GiB","downloaded":"1 GiB","ratio":"50","buffer":"124 GiB","seeding":0,"leeching":0,"seedbonus":"0.00","hit_and_runs":0}
```

Example:
```
https://unit3d.site/api/user?api_token=YOURTOKENHERE
```
