#!/usr/bin/env bash
#
# Provisions the public Cloudflare Tunnel ingress for tracker.iamanro.dev.
#
# Everything except creating the Cloudflare API token is automated: the tunnel,
# its ingress rules, the proxied DNS route, and the connector service inside the
# dedicated `cloudflared` LXC on PVE.
#
# Create the token at https://dash.cloudflare.com/profile/api-tokens with:
#   Account | Cloudflare Tunnel  | Edit
#   Account | Account Settings   | Read
#   Zone    | DNS                | Edit   (zone iamanro.dev)
#
# Usage: CLOUDFLARE_API_TOKEN=... ./scripts/setup-cloudflare-tunnel.sh
#        (unset: the script prompts for it without echoing)

set -euo pipefail
# An API error must abort immediately instead of feeding empty output into the
# next parser in the pipeline.
set -o errtrace
trap 'exit 1' ERR

ZONE_NAME="${ZONE_NAME:-iamanro.dev}"
CLOUDFLARE_ACCOUNT_ID="${CLOUDFLARE_ACCOUNT_ID:-}"
CLOUDFLARE_ZONE_ID="${CLOUDFLARE_ZONE_ID:-}"
TUNNEL_NAME="${TUNNEL_NAME:-vltava}"
PUBLIC_HOSTNAME="${PUBLIC_HOSTNAME:-tracker.iamanro.dev}"
ORIGIN_SERVICE="${ORIGIN_SERVICE:-https://10.0.0.4:443}"
PVE_HOST="${PVE_HOST:-root@100.108.105.119}"
CLOUDFLARED_CT="${CLOUDFLARED_CT:-107}"
API="https://api.cloudflare.com/client/v4"

if [[ -z "${CLOUDFLARE_API_TOKEN:-}" ]]; then
    read -rsp "Cloudflare API token: " CLOUDFLARE_API_TOKEN
    printf '\n'
fi
[[ -n "$CLOUDFLARE_API_TOKEN" ]] || { echo "No API token supplied." >&2; exit 1; }

# cf METHOD PATH [BODY] performs an authenticated API call and fails loudly when
# Cloudflare reports success:false, so no later step runs on partial state.
cf() {
    local method="$1" path="$2" body="${3:-}" response
    if [[ -n "$body" ]]; then
        response=$(curl -sS -X "$method" "$API$path" \
            -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
            -H "Content-Type: application/json" --data "$body")
    else
        response=$(curl -sS -X "$method" "$API$path" \
            -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN")
    fi
    printf '%s' "$response" | python3 -c '
import json, sys
raw = sys.stdin.read()
try:
    payload = json.loads(raw)
except ValueError:
    sys.exit("Cloudflare API returned a non-JSON response: " + raw[:200])
if not payload.get("success"):
    sys.exit("Cloudflare API error: " + json.dumps(payload.get("errors")))
json.dump(payload["result"], sys.stdout)
'
}

# json_field extracts one field from a JSON object on stdin.
json_field() { python3 -c 'import json,sys; print(json.load(sys.stdin)[sys.argv[1]])' "$1"; }

if [[ -z "$CLOUDFLARE_ACCOUNT_ID" ]]; then
    accounts_json=$(cf GET "/accounts")
    CLOUDFLARE_ACCOUNT_ID=$(printf '%s' "$accounts_json" | python3 -c '
import json, sys
accounts = json.load(sys.stdin)
if len(accounts) != 1:
    sys.exit("Set CLOUDFLARE_ACCOUNT_ID to the account that owns the tunnel.")
print(accounts[0]["id"])
')
fi

if [[ -z "$CLOUDFLARE_ZONE_ID" ]]; then
    echo "==> Resolving zone $ZONE_NAME"
    zones_json=$(cf GET "/zones?name=$ZONE_NAME")
    CLOUDFLARE_ZONE_ID=$(printf '%s' "$zones_json" | python3 -c '
import json, sys
zones = json.load(sys.stdin)
if len(zones) != 1:
    sys.exit("Set CLOUDFLARE_ZONE_ID when the token cannot list the DNS zone.")
print(zones[0]["id"])
')
fi

account_id="$CLOUDFLARE_ACCOUNT_ID"
zone_id="$CLOUDFLARE_ZONE_ID"

echo "==> Reusing or creating tunnel $TUNNEL_NAME"
tunnels_json=$(cf GET "/accounts/$account_id/cfd_tunnel?name=$TUNNEL_NAME&is_deleted=false")
tunnel_id=$(printf '%s' "$tunnels_json" | python3 -c '
import json, sys
tunnels = json.load(sys.stdin)
print(tunnels[0]["id"] if tunnels else "")
')

if [[ -z "$tunnel_id" ]]; then
    secret=$(head -c 32 /dev/urandom | base64)
    created_json=$(cf POST "/accounts/$account_id/cfd_tunnel" \
        "$(python3 -c 'import json,sys; print(json.dumps({"name": sys.argv[1], "tunnel_secret": sys.argv[2], "config_src": "cloudflare"}))' "$TUNNEL_NAME" "$secret")")
    tunnel_id=$(printf '%s' "$created_json" | json_field id)
    echo "    created $tunnel_id"
else
    echo "    reusing $tunnel_id"
fi

echo "==> Publishing $PUBLIC_HOSTNAME → $ORIGIN_SERVICE"
# The nginx origin behind Caddy presents a private self-signed certificate, so
# TLS verification is disabled on that LAN-internal hop only.
config=$(python3 -c '
import json, sys
hostname, service = sys.argv[1], sys.argv[2]
print(json.dumps({"config": {"ingress": [
    {"hostname": hostname, "service": service,
     "originRequest": {"noTLSVerify": True, "httpHostHeader": hostname, "originServerName": hostname}},
    {"service": "http_status:404"},
]}}))
' "$PUBLIC_HOSTNAME" "$ORIGIN_SERVICE")
cf PUT "/accounts/$account_id/cfd_tunnel/$tunnel_id/configurations" "$config" >/dev/null

echo "==> Routing proxied DNS record"
record_body=$(python3 -c '
import json, sys
print(json.dumps({"type": "CNAME", "name": sys.argv[1],
                  "content": sys.argv[2] + ".cfargotunnel.com",
                  "proxied": True, "ttl": 1}))
' "$PUBLIC_HOSTNAME" "$tunnel_id")
records_json=$(cf GET "/zones/$zone_id/dns_records?name=$PUBLIC_HOSTNAME")
record_id=$(printf '%s' "$records_json" | python3 -c '
import json, sys
records = json.load(sys.stdin)
print(records[0]["id"] if records else "")
')
if [[ -n "$record_id" ]]; then
    cf PUT "/zones/$zone_id/dns_records/$record_id" "$record_body" >/dev/null
else
    cf POST "/zones/$zone_id/dns_records" "$record_body" >/dev/null
fi

echo "==> Installing connector token in LXC $CLOUDFLARED_CT"
token_json=$(cf GET "/accounts/$account_id/cfd_tunnel/$tunnel_id/token")
connector_token=$(printf '%s' "$token_json" | python3 -c 'import json,sys; print(json.load(sys.stdin))')
printf 'CLOUDFLARE_TUNNEL_TOKEN=%s\n' "$connector_token" | ssh -o BatchMode=yes "$PVE_HOST" \
    "pct exec $CLOUDFLARED_CT -- sh -lc 'umask 077; cat > /etc/cloudflared/tunnel.env; systemctl enable --now cloudflared; systemctl restart cloudflared'"

echo "==> Waiting for the public endpoint"
for _ in $(seq 1 30); do
    status=$(curl -s -o /dev/null -w '%{http_code}' --max-time 10 "https://$PUBLIC_HOSTNAME/login" || true)
    if [[ "$status" == "200" ]]; then
        echo "    https://$PUBLIC_HOSTNAME/login → HTTP 200"
        exit 0
    fi
    sleep 5
done

echo "    last status: ${status:-none}; check: ssh $PVE_HOST pct exec $CLOUDFLARED_CT -- journalctl -u cloudflared -n 50" >&2
exit 1
