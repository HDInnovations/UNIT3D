# Migrating from Supervisor to Systemd

**Note:** This guide assumes you are using a `sudo` user named `ubuntu` and UNIT3D is installed at `/var/www/html`.

## Why Migrate?

Systemd offers several advantages over Supervisor for managing UNIT3D services:

| Aspect | Supervisor | Systemd |
|--------|-----------|---------|
| **Overhead** | Python daemon, socket communication | Native kernel integration |
| **Memory** | ~20-50MB for supervisord | Already running (init system) |
| **Boot Integration** | Requires separate service | Native system integration |
| **Logging** | File-based | Journald (structured, rotated) |
| **Security** | Basic | Namespaces, sandboxing, cgroups |

## 1. Create Systemd Unit Files

### UNIT3D-Announce

> [!CAUTION]
> Only create this Systemd unit file if usings rust based unit3d-announce!

```sh
sudo nano /etc/systemd/system/unit3d-announce.service
```

Add the following:

```ini
# /etc/systemd/system/unit3d-announce.service
#
# Systemd service unit for UNIT3D-Announce

[Unit]
Description=UNIT3D Announce Tracker
Documentation=https://github.com/Roardom/UNIT3D-Announce
After=network.target mysql.service
Wants=mysql.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/html/unit3d-announce

# Environment file for the tracker
EnvironmentFile=/var/www/html/unit3d-announce/.env

# Main process
ExecStart=/var/www/html/unit3d-announce/target/release/unit3d-announce

# Restart policy
Restart=on-failure
RestartSec=5

# Resource limits (adjust based on your server)
# UNIT3D-Announce is very lightweight (~50k req/s per core)
LimitNOFILE=65535

# Security hardening
NoNewPrivileges=true
ProtectSystem=strict
ProtectHome=true
PrivateTmp=true
ReadWritePaths=/var/www/html/unit3d-announce /var/www/html/storage/logs

# Logging - goes to journald, viewable with: journalctl -u unit3d-announce
StandardOutput=journal
StandardError=journal
SyslogIdentifier=unit3d-announce

[Install]
WantedBy=multi-user.target
```

### Laravel Queue Workers

Using a template unit allows you to run multiple queue workers:

```sh
sudo nano /etc/systemd/system/unit3d-queue@.service
```

Add the following:

```ini
# /etc/systemd/system/unit3d-queue@.service
#
# Systemd template unit for Laravel Queue Workers

[Unit]
Description=UNIT3D Queue Worker %i
Documentation=https://laravel.com/docs/queues
After=network.target mysql.service redis.service
Wants=mysql.service redis.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/html

# Main process - runs one queue worker
# --tries=1: Retry failed jobs once
# --max-jobs=1000: Restart after processing 1000 jobs (prevents memory leaks)
# --max-time=3600: Restart after 1 hour (ensures fresh connections)
ExecStart=/usr/bin/php /var/www/html/artisan queue:work --tries=1 --max-jobs=1000 --max-time=3600

# Graceful stop - let the current job finish
TimeoutStopSec=3605

# Restart policy
Restart=always
RestartSec=3

# Security hardening
NoNewPrivileges=true
ProtectSystem=strict
ProtectHome=true
PrivateTmp=true
ReadWritePaths=/var/www/html/storage /var/www/html/bootstrap/cache

# Logging
StandardOutput=journal
StandardError=journal
SyslogIdentifier=unit3d-queue-%i

[Install]
WantedBy=multi-user.target
```

### Laravel Reverb (WebSocket Server)

```sh
sudo nano /etc/systemd/system/unit3d-reverb.service
```

Add the following:

```ini
# /etc/systemd/system/unit3d-reverb.service
#
# Systemd service unit for Laravel Reverb (WebSocket server for UNIT3D Chat)

[Unit]
Description=UNIT3D Chat Server (Laravel Reverb)
Documentation=https://laravel.com/docs/reverb
After=network.target mysql.service redis.service
Wants=mysql.service redis.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/html

# Main process
ExecStart=/usr/bin/php /var/www/html/artisan reverb:start --no-interaction

# Graceful shutdown
TimeoutStopSec=30

# Restart policy
Restart=on-failure
RestartSec=5

# Security hardening
NoNewPrivileges=true
ProtectSystem=strict
ProtectHome=true
PrivateTmp=true
ReadWritePaths=/var/www/html/storage /var/www/html/bootstrap/cache

# Logging
StandardOutput=journal
StandardError=journal
SyslogIdentifier=unit3d-reverb

[Install]
WantedBy=multi-user.target
```

## 2. Stop and Disable Supervisor

```sh
sudo supervisorctl stop all
sudo systemctl stop supervisor
sudo systemctl disable supervisor
```

## 3. Enable and Start Systemd Services

```sh
# Reload systemd to recognize new units
sudo systemctl daemon-reload

# UNIT3D-Announce
sudo systemctl enable --now unit3d-announce

# Queue Workers (4 instances, equivalent to numprocs=4)
sudo systemctl enable --now unit3d-queue@{1..4}

# Laravel Reverb
sudo systemctl enable --now unit3d-reverb
```

## 4. Verify Services

Check all services are running:

```sh
systemctl status unit3d-announce
systemctl status unit3d-queue@1 unit3d-queue@2 unit3d-queue@3 unit3d-queue@4
systemctl status unit3d-reverb
```

View logs:

```sh
journalctl -u unit3d-announce -f
journalctl -u unit3d-queue@* -f
journalctl -u unit3d-reverb -f
```

## 5. Remove Supervisor Configuration (Optional)

Only after confirming everything works:

```sh
sudo rm /etc/supervisor/conf.d/unit3d.conf
sudo apt remove supervisor  # or keep it for other services
```

## Managing Services

### Start/Stop/Restart

```sh
# Single service
sudo systemctl restart unit3d-announce

# All queue workers at once
sudo systemctl restart 'unit3d-queue@*'

# Or individually
sudo systemctl restart unit3d-queue@1
```

### Scaling Queue Workers

Add more workers:

```sh
sudo systemctl enable unit3d-queue@5 unit3d-queue@6
sudo systemctl start unit3d-queue@5 unit3d-queue@6
```

Remove workers:

```sh
sudo systemctl stop unit3d-queue@5 unit3d-queue@6
sudo systemctl disable unit3d-queue@5 unit3d-queue@6
```

### View Logs

```sh
# Follow logs in real-time
journalctl -u unit3d-announce -f

# Show last 100 lines
journalctl -u unit3d-queue@1 -n 100

# Show logs since boot
journalctl -u unit3d-reverb -b

# Show logs for a time range
journalctl -u unit3d-announce --since "1 hour ago"
```

## Troubleshooting

### Service Won't Start

```sh
# Check the full error
journalctl -u unit3d-announce -n 50 --no-pager

# Check service configuration
systemctl cat unit3d-announce

# Test the command manually
sudo -u www-data /var/www/html/unit3d-announce/target/release/unit3d-announce
```

### Permission Issues

The unit files use `ProtectSystem=strict` for security. If you get permission errors:

```sh
# Check what paths the service can write to
systemctl show unit3d-announce -p ReadWritePaths

# Temporarily disable protection for debugging
sudo systemctl edit unit3d-announce
```

Add:

```ini
[Service]
ProtectSystem=false
```

### Queue Jobs Not Processing

```sh
# Check all queue worker statuses
systemctl list-units 'unit3d-queue@*'

# Restart all workers
sudo systemctl restart 'unit3d-queue@*'

# Check for failed jobs
php /var/www/html/artisan queue:failed
```

## See also

For further details, refer to the [systemd documentation](https://www.freedesktop.org/software/systemd/man/systemd.service.html).
