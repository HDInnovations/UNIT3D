# Research: Official Metadata Sources for Music & Book Uploads (Games/IGDB Baseline)

Status: research only — no production code changed.
Scope: identify first-party, official APIs suitable for automatic metadata lookup during upload, mirroring the existing IGDB integration (`app/Services/Igdb/*`, `app/Models/Igdb*`, `app/Enums/GlobalRateLimit.php`) for music and books.

## 1. Existing baseline: IGDB (games)

The current integration (`IgdbClient`, `IgdbScraper`, `ProcessIgdbGameJob`, `GlobalRateLimit::IGDB`) already implements the correct pattern to replicate:

- **Auth**: Twitch OAuth2 Client Credentials flow. `POST https://id.twitch.tv/oauth2/token` with `client_id`, `client_secret`, `grant_type=client_credentials` → bearer `access_token` (cached until `expires_in`). Every IGDB call sends `Client-ID` header + `Authorization: Bearer <token>`. Requires a Twitch Developer application (Client Type: Confidential) — two secrets (`TWITCH_CLIENT_ID`, `TWITCH_CLIENT_SECRET`) already present in config. [api-docs.igdb.com]
- **Rate limit**: 4 requests/second, max 8 concurrent open requests; HTTP 429 on excess, requires backoff. [api-docs.igdb.com]
- **Record ID / search-by-ID**: numeric IGDB `id`; the existing client queries `where id = {$id}` via the Apicalypse query language (`POST /v4/games`) — direct ID lookup is supported and is what UNIT3D already uses.
- **Cover/images**: `cover.image_id` returned; images served from a separate IGDB image CDN by `image_id` (existing code already does this for cover/artwork/company logo `image_id` fields).
- **Attribution/licensing**: Free for non-commercial use under the Twitch Developer Services Agreement; the IGDB API/Data Commercial Usage Addendum requires a **linked "Games metadata is powered by IGDB.com"** credit on any page using IGDB data/images. Commercial use requires a separate partnership. [api-docs.igdb.com; IGDB Commercial Usage Addendum via loomkeep PR #270 citing the addendum text]

This is the shape music/book integrations should match: OAuth-or-keyless client → cached token if applicable → ID-based lookup query → separate image reference → required attribution string.

## 2. Music: MusicBrainz + Cover Art Archive

### MusicBrainz API (metadata: artist/release-group/release/recording)
- **Official docs**: https://musicbrainz.org/doc/MusicBrainz_API , rate limiting: https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting
- **Auth**: None required for read-only lookups (GET requests). OAuth2 or HTTP digest auth is only required for *write*/user-scoped calls, which UNIT3D would not use. [musicbrainz.org/doc/MusicBrainz_API]
- **Rate limit**: Strict **1 request/second average per IP/client**; bursting is not tolerated (they don't apportion excess — sustained >1 req/s gets 100% of requests declined with HTTP 503 until the rate drops). A descriptive `User-Agent` header identifying the application and contact info is mandatory — server admins block IPs/agents that don't comply. [musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting]
- **Record IDs / search-by-ID**: Every MusicBrainz entity (artist, release, release-group, recording, label) has a stable **MBID** (UUID). Direct lookup is `GET /ws/2/release/{mbid}?fmt=json&inc=...`; text search is `GET /ws/2/release/?query=...&fmt=json`. MBIDs are the canonical join key with the Cover Art Archive.
- **License/attribution**: **Core data (artists, releases, recordings, tracks) is CC0** — public domain, no attribution legally required. **Supplementary/user-contributed data (annotations, tags, ratings, edit history) is CC BY-NC-SA 3.0**, which *does* require attribution and is non-commercial-only. [musicbrainz.org/doc/About/Data_License] → For a torrent upload form using only core release/recording/artist metadata, no attribution is legally mandated, though crediting MusicBrainz is good practice and required if any supplementary (tags/annotations) fields are surfaced.
- **Images**: MusicBrainz itself does not serve cover art; it stores the MBID index that Cover Art Archive uses.

### Cover Art Archive (cover images, official MusicBrainz/Internet Archive joint project)
- **Official docs**: https://musicbrainz.org/doc/Cover_Art_Archive/API
- **Auth**: None.
- **Rate limit**: **None currently enforced** at `coverartarchive.org` (explicitly documented as no rate limiting rules, unlike the main MusicBrainz API). [musicbrainz.org/doc/Cover_Art_Archive/API]
- **Lookup**: Indexed **by MusicBrainz Release MBID**: `GET https://coverartarchive.org/release/{mbid}` returns a JSON list of image entries (front/back flags, `types`, full-size URL, and `thumbnails` at 250px/500px/1200px). No search-by-ID other than the MBID itself — this is a hard dependency on already having resolved a release MBID via MusicBrainz first.
- **License/attribution**: Images are **copyrighted by their respective rights holders** (typically label/artist) — CAA does not grant a blanket license, "use at your own risk." No attribution string is imposed by CAA itself, but per-image rights remain with the uploader/label, so this is closer to "best-effort embed, not warrantied" than a licensed asset feed.

### Recommended lookup strategy for music
1. Search MusicBrainz by title/artist text query → get candidate release-group/release **MBIDs** (respect 1 req/s, set `User-Agent: <AppName>/<version> (<contact-url-or-email>)`, cache aggressively since core data is CC0/static).
2. On selection, fetch full release detail from MusicBrainz by MBID (`inc=recordings+artist-credits+labels+release-groups`).
3. Fetch cover art from Cover Art Archive by the *same* release MBID (`/release/{mbid}`); if 404, no cover is archived — fall back to manual upload.
4. Cache MusicBrainz responses long-term (data changes rarely and rate limit is severe); do not re-poll per view.

## 3. Books: Open Library vs. Google Books

### Open Library API (Internet Archive)
- **Official docs**: https://openlibrary.org/dev/docs/api/ , Read API: https://openlibrary.org/dev/docs/api/read , Covers: https://openlibrary.org/dev/docs/api/covers , general API policy: https://openlibrary.org/developers/api
- **Auth**: **None required for any read endpoint** — fully keyless/tokenless. OAuth is only needed for write operations (adding/editing records), not applicable here.
- **Rate limit**: No hard published cap, but Open Library's stated policy is a "polite" ceiling of **~100 requests / 5 minutes per IP**, beyond which HTTP 403 responses appear; they explicitly prioritize open-source/library-style, low-volume, human-facing lookup use (a private tracker's on-demand upload lookup fits this profile). A descriptive `User-Agent` with app name + contact is requested for frequent callers. Cover images specifically: **do not crawl** `covers.openlibrary.org` in bulk — on-demand per-upload fetches are fine, bulk scraping is not. [openlibrary.org/developers/api; openlibrary.org/dev/docs/api/covers]
- **Record IDs / search-by-ID**: Multiple stable IDs — **OLID** (Open Library ID, e.g. `OL7353617M` for editions, `OL45804W` for works), plus direct lookup by **ISBN**, LCCN, OCLC via `/api/books?bibkeys=ISBN:...` or `/isbn/{isbn}.json`. Works (`/works/{id}.json`) vs. Editions (`/books/{id}.json`) are modeled separately, so ISBN naturally resolves to a specific printing/edition while OLID `W`-prefixed IDs represent the abstract work.
- **Covers**: `https://covers.openlibrary.org/b/{key}/{value}-{size}.jpg` where `key` ∈ {isbn, oclc, lccn, olid, id} and `size` ∈ {S,M,L}. A courtesy backlink to Open Library is requested (not strictly mandated) when covers are shown publicly.
- **License/attribution**: Open Library data is intended to be freely reusable (Internet Archive, library-catalog-derived, CC0-style bibliographic metadata); no enforced attribution beyond the requested courtesy link for cover display.

### Google Books API
- **Official docs**: https://developers.google.com/books/docs/v1/using , Terms: https://developers.google.com/books/terms
- **Auth**: API key **not strictly required** for basic volume/search lookups, but effectively mandatory for reliable results — unauthenticated requests are capped at a very low daily quota (~100/day) and may return incomplete data (including missing cover `imageLinks`). An API key requires a **Google Cloud project** (free tier, no billing needed for default quota).
- **Rate limit**: Default authenticated quota is **10,000 requests/day per key** (shared across all request types), resetting at midnight Pacific; unauthenticated ~100/day. Higher quota requires a quota-increase request to Google.
- **Record IDs / search-by-ID**: Google's own `volumeId` (opaque, Google-internal) via `GET /books/v1/volumes/{volumeId}`; text/ISBN search via `GET /books/v1/volumes?q=isbn:{isbn}`. No stable cross-catalog ID beyond ISBN — Google volume IDs are not portable metadata identifiers the way MBIDs/OLIDs are.
- **Covers**: `imageLinks.thumbnail`/`smallThumbnail` URLs from Google's own CDN; higher resolutions are inconsistently available and links can require an API key context to stay valid.
- **License/attribution**: Per Google's Terms of Service, you may not charge users for an app built on Books API data without a separate agreement, and infringing content must be removed on notice. Google can suspend API access for ToS violations. No mandatory "powered by" badge is imposed by the terms in the way IGDB's is, but the terms are more restrictive than Open Library's (usage-based suspension risk, quota is a hard Google-controlled ceiling rather than a self-policed courtesy limit).

### Recommendation: Open Library as primary, Google Books as fallback
- **Primary: Open Library.** No API key/credential management (nothing to provision or leak, unlike IGDB's Twitch secret or a Google Cloud key), no daily request ceiling tied to a paid/managed console, ISBN-first lookup matches how physical/digital book releases are already identified on a tracker, and both Works and Editions expose stable public IDs (OLID) directly analogous to MusicBrainz MBIDs / IGDB IDs.
- **Fallback: Google Books**, used when Open Library has no record for a given ISBN/title (Open Library's catalog, while large, has known coverage gaps versus Google's scanned-book index) or when Open Library returns no cover image. Requires a Google Cloud project + API key, added as a queued/cached job exactly like IGDB, to stay within the 10k/day quota and avoid inline blocking on upload.
- Cache both aggressively (bibliographic metadata rarely changes) and always resolve by ISBN first, falling back to title/author text search only when no ISBN is provided.

## 4. Cross-source strategy & why manual entry stays mandatory

| Medium | Primary source | ID used | Fallback | Auth needed |
|---|---|---|---|---|
| Games | IGDB (existing) | IGDB numeric `id` | — | Twitch Client-ID/Secret |
| Music | MusicBrainz (+ Cover Art Archive for images) | MusicBrainz MBID (UUID) | manual entry | None (User-Agent only) |
| Books | Open Library | ISBN / OLID | Google Books (by ISBN) | None (Open Library); API key (Google) |

**Precedence strategy**: For each medium, attempt lookup by the most specific identifier the uploader already has (ISBN for books, MBID for a known release, IGDB id for games) before falling back to free-text search; free-text search results are presented as *candidates to confirm*, never auto-applied, since title/artist/author string matching across independent catalogs is inherently ambiguous (same album reissued under multiple MBIDs, same book under multiple editions/ISBNs, same game ported across platforms with distinct IGDB ids).

**Why the upload form must always keep a manual-entry path (non-negotiable, independent of source choice):**
1. **Coverage gaps are real and source-specific** — MusicBrainz/Cover Art Archive cover art is user-submitted and absent for many releases (especially obscure/regional/self-released music); Open Library and Google Books both have catalog gaps for small-press, self-published, or non-English works; IGDB may lack unreleased/very new/homebrew titles.
2. **Rate limits force graceful degradation** — MusicBrainz's 1 req/s hard ceiling and Open Library's ~100/5min courtesy ceiling mean bursts of concurrent uploads (e.g., a batch importer) can legitimately get throttled (503/403); the upload flow cannot block indefinitely on an external service and must let the uploader proceed by hand.
3. **Licensing/rights uncertainty on images** — Cover Art Archive explicitly disclaims any warranty on image rights ("use at your own risk"); Google Books' ToS carries take-down obligations; a tracker must be able to skip auto-fetched cover art per-upload (e.g., swap for a different edition's art, or omit it) rather than being locked to whatever the API returned.
4. **Match ambiguity** — none of these APIs guarantees a unique correct match from a fuzzy title search (album re-recordings, book translations/editions, game remasters); a human confirmation/override step is required before metadata is persisted, mirroring how the existing IGDB flow queues a job by a *confirmed* `id`, not by blind text search.

## Sources
- IGDB API docs (Getting Started, auth, rate limit): https://api-docs.igdb.com/
- IGDB attribution requirement (Commercial Usage Addendum quoted in): https://github.com/Logan2234/loomkeep/pull/270
- MusicBrainz API: https://musicbrainz.org/doc/MusicBrainz_API
- MusicBrainz API Rate Limiting: https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting
- MusicBrainz Data License: https://musicbrainz.org/doc/About/Data_License
- Cover Art Archive API: https://musicbrainz.org/doc/Cover_Art_Archive/API
- Cover Art Archive overview: https://musicbrainz.org/doc/Cover_Art_Archive
- Open Library APIs overview / usage policy: https://openlibrary.org/developers/api
- Open Library Read API: https://openlibrary.org/dev/docs/api/read
- Open Library Covers API: https://openlibrary.org/dev/docs/api/covers
- Google Books API — Using the API: https://developers.google.com/books/docs/v1/using
- Google Books API — Terms of Service: https://developers.google.com/books/terms
