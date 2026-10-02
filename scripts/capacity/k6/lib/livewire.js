// Real Livewire 4 wire protocol (see vendor/livewire/livewire/src/Mechanisms/
// HandleRequests/HandleRequests.php and FrontendAssets.php) -- NOT a
// substitute JSON endpoint. The update endpoint's path is derived from
// APP_KEY (`EndpointResolver::prefix()`), so it must be read off the actual
// rendered page, never hardcoded/guessed.
import http from 'k6/http';
import { parseHTML } from 'k6/html';

// This application renders @livewireScriptConfig for its bundled client.
export function extractLivewireConfig(html) {
  const match = html.match(/window\.livewireScriptConfig\s*=\s*(\{[^<]*?\});/);
  if (!match) throw new Error('Rendered Livewire script configuration is missing.');
  const config = JSON.parse(match[1]);
  if (!config.uri || !config.csrf) throw new Error('Rendered Livewire update URI or CSRF token is missing.');
  return { updateUri: config.uri, csrfToken: config.csrf };
}

// Finds the `wire:snapshot="..."` attribute belonging to the component whose
// decoded `memo.name` matches `componentName` (a page can render more than
// one Livewire component). k6/html's `.attr()` already HTML-entity-decodes
// the value, matching what the real Livewire JS client does when reading it
// off the DOM.
export function extractSnapshot(html, componentName) {
  const doc = parseHTML(html);
  let found = null;

  const components = doc.find('[wire\\:snapshot]');
  components.each((index) => {
    if (found) return;
    const raw = components.eq(index).attr('wire:snapshot');
    if (!raw) return;
    try {
      const parsed = JSON.parse(raw);
      if (parsed && parsed.memo && parsed.memo.name === componentName) {
        found = raw; // keep the raw JSON string verbatim -- Livewire expects it back exactly as given, not re-serialized.
      }
    } catch (e) {
      // not a valid snapshot attribute; skip
    }
  });

  if (!found) {
    throw new Error(`No wire:snapshot found for component "${componentName}" on this page.`);
  }

  return found;
}

// POSTs one Livewire component update/call. `snapshot` must be the raw JSON
// string (not a re-stringified object) most recently known for this
// component -- either freshly extracted from a GET page or the `snapshot`
// field of a previous `livewireUpdate()` response, per Livewire's own
// "re-sync from the last response" contract.
export function livewireUpdate(jar, updateUri, csrfToken, snapshot, { updates = {}, calls = [] } = {}, tags = {}, clientHeaders) {
  const body = JSON.stringify({
    _token: csrfToken,
    components: [
      {
        snapshot,
        updates,
        calls,
      },
    ],
  });

  const res = http.post(updateUri, body, {
    jar,
    headers: {
      ...clientHeaders,
      'Content-Type': 'application/json',
      'X-Livewire': 'true',
      'X-CSRF-TOKEN': csrfToken,
    },
    tags,
  });

  let json = null;
  try {
    json = JSON.parse(res.body);
  } catch (e) {
    // leave json null; caller's checks on `res`/`json` will fail visibly
  }

  return { res, json };
}
