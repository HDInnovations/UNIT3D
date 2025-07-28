# Tracker Healthcheck Integration

Follow this guide to configure, deploy (ideally on a separate host), schedule a tracker health checker script, and enable a live health-status badge in your UNIT3D Laravel project via an environment variable.


The `tools/` directory in your UNIT3D project contains:

- `tracker_healthcheck.py`
- `.env.example`

---

## 1. Prepare the Script’s Environment File

1. Copy the example to create your own env file:
   ```bash
   cp tools/.env.example tools/.env
   ```

2. Open `tools/.env` and replace each placeholder accord



---

## 2. Deploy

> [!TIP]
> Ideally, run the healthcheck on its own small server or VM rather than the same host as UNIT3D.

1. Copy only the two files to your monitor host under `/usr/local/bin`:
   ```bash
   scp tools/tracker_healthcheck.py tools/.env user@monitor:/usr/local/bin/
   ```
2. On the monitor host:
   ```bash
   cd /usr/local/bin
   chmod 600 .env
   chmod +x tracker_healthcheck.py
   ```
3. Verify Python 3 is installed:
   ```bash
   python3 --version
   ```

---

## 3. Schedule the Healthcheck

Use cron (or systemd) to run every 5 minutes (adjust to your needs):

```bash
crontab -e
```

```cron
*/5 * * * * cd /usr/local/bin && ./tracker_healthcheck.py >> /var/log/tracker_healthcheck.log 2>&1
```

- Adjust the interval or log path as needed.
- Ensure the cron user can read `/usr/local/bin/.env`, execute the script, and write to the log.

---

## 4.Generate Your Healthchecks.io Badge URL

If you set `PING_URL` in `tools/.env` to your Healthchecks.io ping endpoint:

1. In Healthchecks.io, open your check’s detail page.
2. Click **Badges** and select the tag/slug you used (e.g. `unit3d-announce`).
3. Copy the **badge URL**, which looks like:
   ```
   https://healthchecks.io/badge/{CHECK_UUID}/{SLUG}.svg
   ```
4. Badge states:
- **up** – all runs succeeded
- **down** – at least one run failed
- **late** – (optional) a run is late but not yet failed

Example badge URL:
```
https://healthchecks.io/badge/8655ab89-7e3c-4f20-b63e-951d2b40dc3c/unit3d-announce.svg
```

---

## 5. Enable the Badge in UNIT3D

In your *UNIT3D* project’s root, add the badge URL to your main `.env`:

```
ANNOUNCE_HEALTHCHECK_BADGE_URL="https://healthchecks.io/badge/8655ab89-7e3c-4f20-b63e-951d2b40dc3c/unit3d-announce.svg"
```

UNIT3D will now automatically render the badge in your site's footer when this variable is set.

---

## 6. Troubleshooting

• **Badge is gray/blank**  
– Check the cron log (`/var/log/tracker_healthcheck.log`).  
– Verify `/usr/local/bin/.env` exists and has all required keys.  
– Confirm network access from the monitor host to your tracker and Healthchecks.io.

• **Permission errors**  
– Ensure `tracker_healthcheck.py` is executable (`chmod +x`).  
– Ensure `.env` is readable [only] by the user running the cron (`chmod 600`).

> [!IMPORTANT]
• **Avoid overloading your tracker**  
– Do not run checks more frequently than necessary. Adjust the cron interval to balance detection speed with any rate limits you have in place.