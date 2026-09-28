const port = Number(Bun.env.PORT ?? 9117);
const unit3dUrl = required("UNIT3D_URL").replace(/\/$/, "");


type TorrentAttributes = {
  name: string;
  details_link: string;
  download_link: string;
  size: number;
  created_at: string;
  seeders: number;
  leechers: number;
  times_completed: number;
  category_id: number;
  imdb_id?: string | number;
  tmdb_id?: string | number;
  tvdb_id?: string | number;
};

type Unit3dResponse = { data: Array<{ id: string; attributes: TorrentAttributes }> };

class Unit3dAuthorizationError extends Error {}

Bun.serve({
  port,
  fetch: async (request) => {
    const url = new URL(request.url);
    if (url.pathname !== "/api") return new Response("Not found", { status: 404 });
    const unit3dToken = url.searchParams.get("apikey");
    if (!unit3dToken) return xml(error(100, "UNIT3D API token required"), 401);

    const type = url.searchParams.get("t");
    if (type === "caps") return xml(caps());
    if (!["search", "tvsearch", "movie"].includes(type ?? "")) return xml(error(202, "Unsupported Torznab query"));

    try {
      const torrents = await search(url, type!, unit3dToken);
      return xml(results(torrents));
    } catch (cause) {
      if (cause instanceof Unit3dAuthorizationError) return xml(error(100, "Invalid UNIT3D API token"), 401);
      console.error(cause);
      return xml(error(900, "UNIT3D index request failed"));
    }
  },
});

async function search(query: URL, type: string, unit3dToken: string): Promise<Unit3dResponse["data"]> {
  const params = new URLSearchParams({ driver: "sql", perPage: "100", sortField: "created_at", sortDirection: "desc" });
  const term = query.searchParams.get("q");
  if (term) params.set("name", term);
  for (const [torznab, unit3d] of [["imdbid", "imdbId"], ["tmdbid", "tmdbId"], ["tvdbid", "tvdbId"], ["season", "seasonNumber"], ["ep", "episodeNumber"]] as const) {
    const value = query.searchParams.get(torznab);
    if (value) params.set(unit3d, torznab === "imdbid" ? value.replace(/^tt/i, "") : value);
  }
  const category = type === "movie" ? "1" : type === "tvsearch" ? "2" : categoryFromTorznab(query.searchParams.get("cat"));
  if (category) params.append("categories[]", category);

  const response = await fetch(`${unit3dUrl}/api/torrents/filter?${params}`, {
    headers: { Authorization: `Bearer ${unit3dToken}`, Accept: "application/json" },
  });
  if (response.status === 401 || response.status === 403) throw new Unit3dAuthorizationError();
  if (!response.ok) throw new Error(`UNIT3D returned ${response.status}`);
  return (await response.json() as Unit3dResponse).data;
}

function categoryFromTorznab(value: string | null): string | undefined {
  if (!value) return undefined;
  const categories = value.split(",").map((category) => Number(category) % 100000);
  if (categories.some((category) => category >= 2000 && category < 3000)) return "1";
  if (categories.some((category) => category >= 5000 && category < 6000)) return "2";
  return undefined;
}

function caps(): string {
  return `<?xml version="1.0" encoding="UTF-8"?><caps><server version="1.0" title="UNIT3D Torznab"/><limits max="100" default="100"/><searching><search available="yes" supportedParams="q"/><tv-search available="yes" supportedParams="q,season,ep,imdbid,tvdbid,tmdbid"/><movie-search available="yes" supportedParams="q,imdbid,tmdbid"/></searching><categories><category id="2000" name="Movies"/><category id="5000" name="TV"/></categories></caps>`;
}

function results(torrents: Unit3dResponse["data"]): string {
  const items = torrents.map(({ id, attributes }) => {
    const category = attributes.category_id === 2 ? 5000 : 2000;
    const optional = [
      tag("imdb", torznabImdb(attributes.imdb_id)), tag("tmdbid", attributes.tmdb_id), tag("tvdbid", attributes.tvdb_id),
    ].join("");
    return `<item><title>${escape(attributes.name)}</title><guid isPermaLink="false">${escape(id)}</guid><link>${escape(attributes.details_link)}</link><pubDate>${new Date(attributes.created_at).toUTCString()}</pubDate><size>${attributes.size}</size><category>${category}</category><enclosure url="${escape(attributes.download_link)}" length="${attributes.size}" type="application/x-bittorrent"/><torznab:attr name="seeders" value="${attributes.seeders}"/><torznab:attr name="peers" value="${attributes.seeders + attributes.leechers}"/><torznab:attr name="grabs" value="${attributes.times_completed}"/>${optional}</item>`;
  }).join("");
  return `<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:torznab="http://torznab.com/schemas/2015/feed"><channel><title>UNIT3D Torznab</title>${items}</channel></rss>`;
}

function torznabImdb(value: string | number | undefined): string | undefined {
  if (value === undefined || value === null || value === "" || value === 0) return undefined;
  const imdb = String(value);
  return imdb.startsWith("tt") ? imdb : `tt${imdb}`;
}
function tag(name: string, value: string | number | undefined): string { return value ? `<torznab:attr name="${name}" value="${escape(value)}"/>` : ""; }
function error(code: number, description: string): string { return `<?xml version="1.0" encoding="UTF-8"?><error code="${code}" description="${escape(description)}"/>`; }
function xml(body: string, status = 200): Response { return new Response(body, { status, headers: { "content-type": "application/xml; charset=utf-8" } }); }
function escape(value: string | number): string { return String(value).replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;").replaceAll("'", "&apos;"); }
function required(name: string): string { const value = Bun.env[name]; if (!value) throw new Error(`${name} is required`); return value; }
