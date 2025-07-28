#!/usr/bin/env python3
import os, sys, re, binascii, socket, argparse
from pathlib import Path
from urllib.request import Request, urlopen
from urllib.parse   import quote_from_bytes

def ts():
    from datetime import datetime
    return datetime.now().strftime("%Y-%m-%d %H:%M:%S")

parser = argparse.ArgumentParser()
parser.add_argument('--verbose', action='store_true', help='Enable verbose output')
args = parser.parse_args()

def verbose(msg):
    if args.verbose:
        print(f"{ts()} VERBOSE: {msg}")

here = Path(__file__).parent
envf = here / ".env"
if not envf.exists():
    print(f"{ts()} ERROR: .env not found at {envf}", file=sys.stderr)
    sys.exit(1)

env = {}
for line in envf.read_text().splitlines():
    line = line.strip()
    if not line or line.startswith("#"):
        continue
    k, _, v = line.partition("=")
    env[k.strip()] = v.strip().strip('"').strip("'")

REQ = ("TRACKER_BASE_URL","PASSKEY","INFO_HASH","PEER_ID","PORT","UPLOADED","DOWNLOADED","LEFT")
for k in REQ:
    if not env.get(k):
        print(f"{ts()} ERROR: {k} not set in .env", file=sys.stderr)
        sys.exit(1)

PING_URL = env.get("PING_URL","").rstrip("/")
NUMWANT = env.get("NUMWANT","50")

def pct(h):
    try:
        raw = binascii.unhexlify(h)
    except binascii.Error:
        return None
    return quote_from_bytes(raw)

ih = pct(env["INFO_HASH"])
pid = pct(env["PEER_ID"])
verbose(f"INFO_HASH={ih}, PEER_ID={pid}")
if ih is None or pid is None:
    message = f"{ts()} TRACKER DOWN: Invalid hex in INFO_HASH or PEER_ID"
    print(message)
    exit_code = 1
else:
    base = env["TRACKER_BASE_URL"].rstrip("/")
    url = (
        f"{base}/{env['PASSKEY']}?"
        f"info_hash={ih}&peer_id={pid}&port={env['PORT']}"
        f"&uploaded={env['UPLOADED']}&downloaded={env['DOWNLOADED']}"
        f"&left={env['LEFT']}&numwant={NUMWANT}"
    )
    verbose(f"Announce URL: {url}")
    try:
        req = Request(url, headers={"User-Agent":"HealthCheck/1.0"})
        with urlopen(req, timeout=10) as r:
            body = r.read()
            code = r.getcode()
            verbose(f"HTTP code: {code}")
            verbose(f"Response body: {body}")
    except Exception as e:
        message = f"{ts()} TRACKER DOWN: transport error ({e})"
        print(message)
        exit_code = 1
    else:
        if code != 200:
            message = f"{ts()} TRACKER DOWN: HTTP {code}"
            print(message)
            exit_code = 1
        elif not body:
            message = f"{ts()} TRACKER DOWN: empty response"
            print(message)
            exit_code = 1
        else:
            m = re.search(rb"14:failure reason(\d+):", body)
            if m:
                n, mend = int(m.group(1)), m.end()
                reason = body[mend:mend+n].decode("utf-8", errors="replace")
                message = f"{ts()} TRACKER DOWN: {reason}"
                print(message)
                exit_code = 1
            else:
                message = f"{ts()} TRACKER UP: announce looks valid"
                print(message)
                exit_code = 0

if PING_URL:
    target = PING_URL if exit_code == 0 else f"{PING_URL}/fail"
    verbose(f"Pinging: {target}")
    try:
        req = Request(target, data=message.encode("utf-8"), method="POST")
        urlopen(req, timeout=10)
    except socket.error as e:
        print(f"{ts()} Ping failed: {e}", file=sys.stderr)

sys.exit(exit_code)