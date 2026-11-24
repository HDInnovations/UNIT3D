# Migrating UNIT3D Chat Server

**Note:** This guide assumes you are using a `sudo` user named `ubuntu`, UNIT3D is installed at `/var/www/html`, and nginx is your web server.

## 1. Update UNIT3D

```sh
cd /var/www/html
sudo php artisan down
sudo php artisan git:update
```

Follow the prompts as described in [Updating UNIT3D](updating_unit3d_version.md).

## 2. Build and install zatat

zatat does not publish binaries yet, so you compile it once. You can build on the server or on any Linux machine with the same architecture and copy the binary over.

1. **Install the build tools and Rust** (as a normal user, not root):

    ```sh
    sudo apt install -y build-essential pkg-config libssl-dev cmake perl clang libclang-dev git
    curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh
    . "$HOME/.cargo/env"
    ```

2. **Get the pinned release and build it:**

    ```sh
    git clone --branch v0.2.5 --depth 1 https://github.com/Dokan-E-Commerce/zatat.git ~/zatat
    cd ~/zatat
    cargo build --release --locked --bin zatat
    ```

3. **Install the binary and create a service user:**

    ```sh
    sudo install -m 0755 target/release/zatat /usr/local/bin/zatat
    sudo useradd --system --no-create-home --shell /usr/sbin/nologin zatat
    zatat --version
    ```

## 3. Configure zatat and `.env`

1. **Generate your own id, key and secret.** Do not use the placeholder values:

    ```sh
    php -r 'echo random_int(100000, 999999), PHP_EOL;'   # PUSHER_APP_ID
    openssl rand -hex 16                                 # PUSHER_APP_KEY
    openssl rand -hex 32                                 # PUSHER_APP_SECRET
    ```

2. **Create the zatat config:**

    ```sh
    sudo mkdir -p /etc/zatat
    sudo nano /etc/zatat/zatat.toml
    ```

    Paste the following and replace the `[[apps]]` values with your own:

    ```toml
    [server]
    # Localhost only. nginx proxies /app/ to it and Laravel publishes events to it locally.
    # It must not be exposed to the internet directly.
    host = "127.0.0.1"
    port = 8080

    # Largest HTTP request body Laravel may publish. Chat payloads such as a user's echoes
    # are sent in a single event and can be much larger than the 10 KB default.
    max_request_size = 1_000_000

    # Horizontal scaling over Redis pub/sub. Only needed with more than one chat server.
    # [server.scaling]
    # enabled = true
    # channel = "zatat"
    #
    # [server.scaling.redis]
    # host = "127.0.0.1"
    # port = 6379
    # db = 0

    # Prometheus metrics, loopback only and without auth by default.
    # [server.prometheus]
    # listen = "127.0.0.1:9090"

    [[apps]]
    id     = "123456"                  # PUSHER_APP_ID
    key    = "your_generated_key"      # PUSHER_APP_KEY
    secret = "your_generated_secret"   # PUSHER_APP_SECRET

    # The host of APP_URL. Only pages on these hosts may open a WebSocket.
    allowed_origins = ["your_domain.tld", "www.your_domain.tld"]

    ping_interval    = 30
    activity_timeout = 30

    # Frames sent by browsers: channel subscriptions and typing whispers are tiny.
    max_message_size = 100_000

    # Safety cap per node. Raise it if you really expect more simultaneous chat users.
    max_connections = 20_000

    max_channels_per_connection = 100

    # Only members of a presence channel may send whispers (typing indicator).
    accept_client_events_from = "members"

    # zatat defaults to 100 members per presence channel (Pusher's hosted limit).
    # UNIT3D chatrooms are presence channels and are usually larger than that.
    max_presence_members_per_channel = 10_000
    max_presence_member_size_bytes   = 4096

    # Throttles a single connection. Pings and whispers count as frames, so keep it generous.
    [apps.rate_limiting]
    enabled            = true
    max_attempts       = 600
    decay_seconds      = 60
    terminate_on_limit = false
    ```

    Then make the file readable by the service user only:

    ```sh
    sudo chown root:zatat /etc/zatat/zatat.toml
    sudo chmod 640 /etc/zatat/zatat.toml
    ```

    Do not lower the presence and request size limits. zatat limits a presence channel to 100 members by default, which would stop users from joining a busy chatroom.

3. **Update `.env`:**

    ```sh
    sudo nano /var/www/html/.env
    ```

    Remove the old websocket variable (on very old installs it may be called `MIX_ECHO_ADDRESS`):

    ```dotenv
    VITE_ECHO_ADDRESS=https://your_domain.tld:8443
    ```

    Switch the broadcast driver and add the chat server settings, using the same values as in `zatat.toml`:

    ```dotenv
    BROADCAST_CONNECTION=pusher

    PUSHER_APP_ID=123456
    PUSHER_APP_KEY=your_generated_key
    PUSHER_APP_SECRET=your_generated_secret
    PUSHER_HOST=127.0.0.1
    PUSHER_PORT=8080
    PUSHER_SCHEME=http

    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    ```

## 4. Add the nginx proxy

1. **Remove** the old `location /socket.io` block (and any `listen 8443` server block) from your nginx site configuration if you were using it that way.

2. **Add the proxy** inside the `server` block that listens on port 443:

    ```nginx
    location /app/ {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_buffering off;
        proxy_read_timeout 86400;
        proxy_pass http://127.0.0.1:8080;
    }
    ```

    The port in `proxy_pass` must match `[server] port` in `zatat.toml`.

3. **Raise the connection limits.** Every chat user keeps two connections open in nginx (browser to nginx, nginx to zatat). In `/etc/nginx/nginx.conf`:

    ```nginx
    worker_rlimit_nofile 65535;

    events {
        worker_connections 16384;
    }
    ```

4. **Test and reload nginx:**

    ```sh
    sudo nginx -t && sudo systemctl reload nginx
    ```

## 5. Run unit3d-chat as a service

1. **Stop and disable the old chatbox service** (the one running `laravel-echo-server start`). If you are still on Supervisor, finish the [Supervisor to Systemd migration](systemd_migration.md) first.

2. **Create the unit:**

    ```sh
    sudo nano /etc/systemd/system/unit3d-chat.service
    ```

    ```ini
    # /etc/systemd/system/unit3d-chat.service
    #
    # Systemd service unit for zatat (WebSocket server for UNIT3D Chat)

    [Unit]
    Description=UNIT3D Chat Server
    Documentation=https://github.com/Dokan-E-Commerce/zatat
    After=network.target mysql.service redis.service
    Wants=mysql.service redis.service

    [Service]
    Type=simple
    User=zatat
    Group=zatat

    ExecStart=/usr/local/bin/zatat start --config /etc/zatat/zatat.toml
    Environment=RUST_LOG=info

    TimeoutStopSec=30
    Restart=on-failure
    RestartSec=5

    # Each connected chatbox user keeps one open file
    LimitNOFILE=65535

    # Security hardening
    NoNewPrivileges=true
    ProtectSystem=strict
    ProtectHome=true
    PrivateTmp=true

    StandardOutput=journal
    StandardError=journal
    SyslogIdentifier=unit3d-chat

    [Install]
    WantedBy=multi-user.target
    ```

3. **Enable and start it:**

    ```sh
    sudo systemctl daemon-reload
    sudo systemctl enable --now unit3d-chat
    sudo systemctl status unit3d-chat
    ```

## 6. Rebuild assets and caches

The `VITE_PUSHER_*` variables are compiled into the JavaScript, so assets must be rebuilt after the `.env` changes in step 3, even if `git:update` already built them.

```sh
cd /var/www/html
sudo bun install
sudo bun run build
sudo php artisan clear:all_cache
sudo php artisan up
```

## 7. Verify

1. **Check that zatat is up:**

    ```sh
    curl http://127.0.0.1:8080/health
    ```

    The answer is `ok`.

2. **Check the WebSocket handshake through nginx.** Replace the domain and key. A working setup answers `HTTP/1.1 101 Switching Protocols`:

    ```sh
    curl -i -N \
      -H "Connection: Upgrade" -H "Upgrade: websocket" \
      -H "Sec-WebSocket-Version: 13" -H "Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==" \
      -H "Origin: https://your_domain.tld" \
      "https://your_domain.tld/app/your_PUSHER_APP_KEY?protocol=7"
    ```

    The same request with a different `Origin` must be refused (`4009`).

## 8. Clean up

Once everything works:

```sh
cd /var/www/html
sudo rm -f laravel-echo-server.json laravel-echo-server.lock
```

Also close port `8443` in your firewall if you opened it for the old chatbox.

## Operating unit3d-chat

- **Restart** after changing `/etc/zatat/zatat.toml`: `sudo systemctl restart unit3d-chat`. A deploy of new UNIT3D code does not need a restart, because zatat does not run any UNIT3D code.
- **Logs:** `journalctl -u unit3d-chat -f`. Set `RUST_LOG=debug` temporarily when troubleshooting.
- **Metrics:** uncomment `[server.prometheus]` in `zatat.toml` to expose Prometheus metrics on `127.0.0.1:9090`.
- **Upgrading zatat:** repeat step 2 with the new tag, replace `/usr/local/bin/zatat`, and restart the service.

## Tuning for large sites

- **Connections:** `max_connections` in `zatat.toml` (`20_000` per node in the example above) and the open file limit (`LimitNOFILE` in the systemd unit) must both be high enough. Each connected user needs one file descriptor in zatat and two in nginx.
- **Chat on its own subdomain:** set `VITE_PUSHER_HOST=chat.your_domain.tld` (and `VITE_PUSHER_PORT` / `VITE_PUSHER_SCHEME` if they are not `443` / `https`), add the `/app/` proxy block to that subdomain's nginx server, and rebuild assets. `allowed_origins` still has to contain the host of the page users visit, not the chat subdomain.

## Troubleshooting

| Symptom | Likely cause |
|---------|--------------|
| `502 Bad Gateway` on `/app/...` | zatat is not running, or the nginx `proxy_pass` port differs from `[server] port`. Check `systemctl status unit3d-chat` and `journalctl -u unit3d-chat`. |
| `4001` error | The key the browser uses does not match the `key` in `zatat.toml`. This happens when `PUSHER_APP_KEY` was changed without rebuilding assets (`bun run build`). |
| `4009` error | The page's host is not in `allowed_origins` in `zatat.toml`. |
| Chatbox loads but messages need a page refresh | zatat is reachable by browsers but Laravel cannot publish to it. Check `PUSHER_HOST`, `PUSHER_PORT`, `PUSHER_SCHEME`, that `BROADCAST_CONNECTION=pusher`, and that `PUSHER_APP_ID`, `PUSHER_APP_KEY` and `PUSHER_APP_SECRET` match `zatat.toml`. |
| Users cannot join a busy chatroom | `max_presence_members_per_channel` in `zatat.toml` was left at zatat's default of 100. |
| Large events (echoes) fail to publish | `max_request_size` in `zatat.toml` is too small. Keep it at `1_000_000` or higher. |
| WebSocket blocked by CSP in the browser console | `APP_URL` or `VITE_PUSHER_*` do not match the real domain. Fix them, then run `php artisan clear:all_cache`. |
| `403` on `/broadcasting/auth` | The session expired or the user is logged out. Reload the page and log in again. |
| Connections drop at about 1,000 users | The open file limit is too low (`LimitNOFILE` in the systemd unit), or `worker_connections` in nginx. |
