# Subtitle API Documentation

A small read-only API that lets subtitle managers such as [Bazarr](https://www.bazarr.media) search and download the
subtitles uploaded to your tracker. It is a thin layer over the existing subtitle system:

- It uses the regular API keys, no other credentials are needed. Searching requires the key's **Search** permission
  and downloading requires its **Download** permission.
- Only **approved** subtitles attached to **approved** torrents are ever returned or downloadable. Pending, rejected
  and postponed subtitles, and subtitles of pending, rejected, postponed or deleted torrents, are invisible.
- Downloads use the same code as the website: users whose download rights are revoked cannot download (unless they
  uploaded the subtitle), and every download increments the subtitle's download count.
- The API is limited by the API rate limit (30 requests per minute per user).

Both **movies** and **TV episodes** are supported.

## Setup

### UNIT3D administrator

1. Make sure your tracker runs a UNIT3D version that includes the subtitle API. No configuration or migration is
   needed; the endpoints are enabled with the rest of the API.
2. Make sure the subtitles you want to share are **approved** (subtitles uploaded through the website are approved
   automatically).
3. Users create an API key for Bazarr on their **Settings → API Keys** page (`/users/{username}/apikeys`), with the
   **Search** and **Download** permissions. API keys expire, so a new key must be given to Bazarr when it does.

### Bazarr administrator

1. In Bazarr, go to **Settings → Providers**, add the **UNIT3D** provider and fill in:
    - **UNIT3D URL**: the address of the tracker, e.g. `https://tracker.example.com` (a trailing slash is fine).
    - **API Key**: the API key of the UNIT3D account Bazarr should use.
2. Click **Test Connection**. It shows the UNIT3D version on success, or the reason of the failure (invalid or
   expired API key, API key without the Search or Download permission, denied access, unreachable server, a UNIT3D
   version without the subtitle API, server error).
3. Save. UNIT3D can be ordered among the other providers like any other provider.

Bazarr only sees the subtitles that UNIT3D allows the configured account to access.

## Language codes

Languages are identified by the `code` of UNIT3D's media languages, which are ISO 639-1 two-letter codes (e.g. `en`,
`fr`, `pt`). UNIT3D does not distinguish regional variants, so for example `pt` is always plain Portuguese, never
Brazilian Portuguese.

## Limitations

UNIT3D does not record whether a subtitle is **forced** or for the **hearing impaired**, so both fields are always
`null` (unknown). Bitmap (`.sup`) subtitles are returned by the API but ignored by Bazarr, and `.zip` subtitles are
archives that Bazarr extracts.

A subtitle attached to a **season pack** or a **complete series pack** is returned for every episode it may cover
(with a `pack` value). UNIT3D does not keep the name of the uploaded file, so the episode of a single subtitle file on a
pack is unknown: Bazarr only uses pack subtitles uploaded as a `.zip` archive, from which it extracts the episode.

## Endpoints

All endpoints require the API key and should be called with the `Accept: application/json` header.

### Status

`GET /api/subtitles/status`

Checks that the API key is valid and that the tracker provides the subtitle API. It works with any valid API key and
reports whether the key may search and download subtitles.

#### Example Request

```bash
curl -X GET "https://unit3d.site/api/subtitles/status" \
-H "Authorization: Bearer YOUR_API_KEY_HERE" \
-H "Accept: application/json"
```

#### Example Response

```json
{
  "status": "ok",
  "provider": "unit3d",
  "version": "v9.2.0",
  "api_version": 1,
  "permissions": {
    "search": true,
    "download": true
  }
}
```

### Search Subtitles

`GET /api/subtitles`

Search the subtitles of a movie or a TV episode. At least one of `tmdb_id`, `tvdb_id`, `imdb_id` or `title` is
required.

The movie or show is matched by its TMDB id first, then by its TVDB id (episodes only), then by its IMDb id. Each id is
only used when the previous ones are not given or match nothing. The exact title (show name for episodes) and year
(first air year for episodes) are only used when no id is given. The strategy that was used is returned in
`meta.matched_by` (`tmdb`, `tvdb`, `imdb` or `title`).

An episode search returns the subtitles of the episode's torrents, of its season packs, and of complete series packs.
Specials are season `0`.

#### Query Parameters

| Parameter  | Type    | Description                                                      | Default |
|------------|---------|------------------------------------------------------------------|---------|
| `type`     | string  | `movie` or `episode`                                             | `movie` |
| `tmdb_id`  | integer | TMDB movie ID, or TMDB TV show ID for episodes                   | -       |
| `tvdb_id`  | integer | TVDB show ID (episodes only)                                     | -       |
| `imdb_id`  | string  | IMDb ID of the movie or show, with or without the `tt` prefix    | -       |
| `title`    | string  | Exact movie title or show name, only used when no ID is given    | -       |
| `year`     | integer | Release year, or first air year for episodes; required with `title` | -    |
| `season`   | integer | Season number, `0` for specials (episodes only, required)        | -       |
| `episode`  | integer | Episode number (episodes only, required)                         | -       |
| `language` | string  | Comma-separated language codes (e.g. `en,fr`)                    | all     |
| `perPage`  | integer | Items per page (max: 50)                                         | 25      |
| `page`     | integer | Page number                                                      | 1       |

Results are ordered by most downloaded first.

#### Example Request

```bash
curl -X GET "https://unit3d.site/api/subtitles?tmdb_id=278&language=en,fr" \
-H "Authorization: Bearer YOUR_API_KEY_HERE" \
-H "Accept: application/json"
```

#### Example Response

```json
{
  "data": [
    {
      "id": 123,
      "language": "en",
      "language_name": "English",
      "extension": "srt",
      "filename": "[English.Subtitle]The.Shawshank.Redemption.1994.1080p.BluRay.x264-GRP.srt",
      "size": 12345,
      "downloads": 42,
      "forced": null,
      "hearing_impaired": null,
      "torrent_id": 456,
      "release": "The.Shawshank.Redemption.1994.1080p.BluRay.x264-GRP",
      "type": "movie",
      "season": null,
      "episode": null,
      "pack": null,
      "tmdb_id": 278,
      "tvdb_id": null,
      "imdb_id": "tt0111161",
      "created_at": "2025-08-01T11:02:22+00:00",
      "download_url": "/api/subtitles/123/download"
    }
  ],
  "links": {
    "first": "https://unit3d.site/api/subtitles?tmdb_id=278&language=en%2Cfr&page=1",
    "last": "https://unit3d.site/api/subtitles?tmdb_id=278&language=en%2Cfr&page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {"url": null, "label": "&laquo; Previous", "page": null, "active": false},
      {"url": "https://unit3d.site/api/subtitles?tmdb_id=278&language=en%2Cfr&page=1", "label": "1", "page": 1, "active": true},
      {"url": null, "label": "Next &raquo;", "page": null, "active": false}
    ],
    "path": "https://unit3d.site/api/subtitles",
    "per_page": 25,
    "to": 1,
    "total": 1,
    "matched_by": "tmdb"
  }
}
```

`release` is the name of the torrent the subtitle belongs to. For episodes, `type` is `episode`, `tmdb_id` is the TMDB
TV show ID, and `season`, `episode` and `pack` describe the torrent: a single episode has a `season` and an `episode`,
a season pack has `pack: "season"` and no `episode`, and a complete series pack has `pack: "series"` and neither.

#### Example Episode Request

```bash
curl -X GET "https://unit3d.site/api/subtitles?type=episode&tvdb_id=81189&season=1&episode=5&language=en" \
-H "Authorization: Bearer YOUR_API_KEY_HERE" \
-H "Accept: application/json"
```

### Download Subtitle

`GET /api/subtitles/{id}/download`

Downloads the subtitle file and increments its download count. The response has the file's `Content-Type` and a
`Content-Disposition` attachment header with the same filename as `filename` in the search results.

#### Example Request

```bash
curl -X GET "https://unit3d.site/api/subtitles/123/download" \
-H "Authorization: Bearer YOUR_API_KEY_HERE" \
-H "Accept: application/json" \
-OJ
```

## Error Responses

| Status | Meaning                                                                      |
|--------|------------------------------------------------------------------------------|
| `401`  | Missing or invalid API key                                                   |
| `403`  | The API key lacks the required permission, or the user's download rights are revoked |
| `404`  | Subtitle not found, not approved, or its file is missing                     |
| `422`  | Invalid query parameters                                                     |
| `429`  | Rate limit exceeded                                                          |
