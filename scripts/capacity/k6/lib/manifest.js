// Loaded once per k6 VU-init from the file `capacity:seed-fixtures` wrote
// into the shared `/results` volume. Never contains synthetic/fake data:
// these are the exact passkeys/info_hashes/canary identity that exist in the
// isolated capacity-benchmark database.
export function loadManifest(path = '/results/manifest.json') {
  const raw = open(path);
  const manifest = JSON.parse(raw);

  if (!manifest.passkeys || manifest.passkeys.length === 0) {
    throw new Error(
      `Manifest at ${path} has no passkeys. Run scripts/capacity/bootstrap.sh (capacity:seed-fixtures) first.`
    );
  }
  if (!manifest.info_hashes || manifest.info_hashes.length === 0) {
    throw new Error(`Manifest at ${path} has no info_hashes.`);
  }

  return manifest;
}

export function hexToPercentEncoded(hex) {
  let out = '';
  for (let i = 0; i < hex.length; i += 2) {
    out += '%' + hex.substr(i, 2).toUpperCase();
  }
  return out;
}
