# Local Development with Docker and Laravel Sail

This guide sets up UNIT3D for local development with Docker Compose and [Laravel Sail](https://laravel.com/docs/sail). Everything runs in containers and is served over **HTTPS** at `https://localhost`, like a real site.

OS-specific steps (installing the prerequisites and trusting the certificate) are in the [macOS](local_development_macos.md) and [Arch Linux](local_development_arch_linux.md) guides. The rest is shared and described here.

> [!WARNING]
> This setup is for local development only. It is not suitable for production. For production see [Server Management](server_management.md).

## What runs

`./vendor/bin/sail up -d` starts these containers from [docker-compose.yml](https://github.com/HDInnovations/UNIT3D/blob/development/docker-compose.yml):

| Service | Purpose | Reachable at |
|---------|---------|--------------|
| `nginx` | HTTPS reverse proxy with a self-signed certificate. It also proxies the chatbox WebSocket on `/app/`. | `https://localhost` (port 443). `http://localhost:8000` redirects to HTTPS. |
| `unit3d-core` | The UNIT3D PHP application | Behind nginx. Port 80 is the raw PHP server: do not use it, because session cookies are HTTPS-only. |
| `queue` | Laravel queue worker | - |
| `unit3d-chat` | Chatbox WebSocket server ([zatat](https://github.com/Dokan-E-Commerce/zatat)) | Through nginx on `/app/`, and directly on port 8080 |
| `mysql` | Database | `127.0.0.1:3306` |
| `mariadb` | Alternative database. It starts too, but is only used if you set `DB_HOST=mariadb`. | `127.0.0.1:3307` |
| `redis` | Cache, sessions, queue | `127.0.0.1:6379` |
| `meilisearch` | Search engine | `http://localhost:7700` |
| `mailpit` | Catches outgoing email | Web UI at `http://localhost:8025` |

## 1. Prerequisites

You need Git, Docker with the Compose plugin, and PHP (8.5 or newer) with Composer on your computer. PHP and Composer are only used for the first `composer install`, which creates `./vendor/bin/sail`. Everything after that runs in containers, including Bun for the assets.

See your OS guide for the installation commands: [macOS](local_development_macos.md#prerequisites) or [Arch Linux](local_development_arch_linux.md#prerequisites).

Make sure nothing else on your computer uses ports 443, 3306, 6379, 7700, 8025 or 8080.

## 2. Clone the repository and install PHP dependencies

```bash
git clone https://github.com/HDInnovations/UNIT3D.git
cd UNIT3D
composer install
```

If your PHP is older than the version in `composer.json`, add `--ignore-platform-reqs`. It is only used here to install the packages. The application itself runs with the PHP version inside the container.

Contributions go to the `development` branch (`git checkout development`).

> [!NOTE]
> `composer install` must come first. `./vendor/bin/sail` does not exist until Composer has installed the Laravel Sail package.

## 3. Configure `.env`

```bash
cp .env.example .env
```

Edit `.env` and set these values:

```dotenv
DB_HOST=mysql                                 # The container name in docker-compose.yml
DB_USERNAME=unit3d                            # Anything except `root`
REDIS_HOST=redis                              # The container name in docker-compose.yml
MEILISEARCH_HOST=http://meilisearch:7700      # Not 127.0.0.1: that would be the app container itself
PUSHER_HOST=unit3d-chat                       # The chat server container
```

Keep `SESSION_SECURE_COOKIE=true` and `PUSHER_PORT=8080` from `.env.example`. You do not need to disable the Content Security Policy or HSTS, because the site is served over HTTPS.

Optional, for detailed error pages and catching emails:

```dotenv
APP_ENV=local
APP_DEBUG=true

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Using a domain other than `localhost`

`https://localhost` works without anything else. To use a name like `unit3d.test` instead, set:

```dotenv
APP_URL=https://unit3d.test
SERVER_NAME=unit3d.test
SSL_DOMAIN=unit3d.test
SSL_ALT_NAME=DNS:unit3d.test
```

and point the name at your computer by adding this line to `/etc/hosts`:

```bash
echo "127.0.0.1 unit3d.test" | sudo tee -a /etc/hosts
```

These must be set **before the first `sail up`**, because the certificate is generated once with that name.

## 4. Generate the chat credentials

The chat server needs an app id, key and secret. `.env.example` leaves them blank. Generate your own and put them in `.env`:

```bash
php -r 'echo random_int(100000, 999999), PHP_EOL;'   # PUSHER_APP_ID
openssl rand -hex 16                                 # PUSHER_APP_KEY
openssl rand -hex 32                                 # PUSHER_APP_SECRET
```

```dotenv
PUSHER_APP_ID=123456
PUSHER_APP_KEY=paste_the_key_here
PUSHER_APP_SECRET=paste_the_secret_here
```

The values only matter locally, but they must not be empty. `VITE_PUSHER_APP_KEY` is filled in from `PUSHER_APP_KEY` automatically.

## 5. Start Sail

```bash
./vendor/bin/sail up -d
```

The first start builds the application image and compiles the chat server from source. Thiss can take several minutes.

## 6. Trust the certificate

nginx generates a self-signed certificate the first time it starts. Browsers warn about it, and the chatbox WebSocket may refuse to connect, until your computer trusts it.

Copy it out of the container:

```bash
docker compose cp nginx:/etc/nginx/certs/server.pem ./sail-nginx.pem
```

Then add it to your trusted certificates and restart your browser. The commands depend on your OS: [macOS](local_development_macos.md#trust-the-certificate) or [Arch Linux](local_development_arch_linux.md#trust-the-certificate).

Delete `sail-nginx.pem` afterwards. Do not commit it.

## 7. Application key, database and assets

1. **Generate the application key:**

    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

    If you import a backup of a real site, set `APP_KEY` in `.env` to that site's key instead.

2. **Set up the database.** Choose one:

    - **Sample data** (a fresh database with seeded defaults):

        ```bash
        ./vendor/bin/sail artisan migrate:fresh --seed
        ```

        > [!CAUTION]
        > This deletes everything in the database.

    - **A backup of a real site** (for example `prod-site-backup.sql`, with `APP_KEY` set to that site's key):

        ```bash
        ./vendor/bin/sail mysql < prod-site-backup.sql
        ```

        `sail mysql` connects with `DB_USERNAME` and `DB_DATABASE` from `.env` and ignores any extra options, so do not add `-u` or `-p`.

3. **Sync the search index** so that torrent search works:

    ```bash
    ./vendor/bin/sail artisan scout:sync-index-settings
    ./vendor/bin/sail artisan auto:sync_torrents_to_meilisearch --wipe
    ./vendor/bin/sail artisan auto:sync_people_to_meilisearch
    ```

4. **Compile the assets.** Do this again whenever you change `PUSHER_APP_KEY`, because it is compiled into the JavaScript:

    ```bash
    ./vendor/bin/sail bun install
    ./vendor/bin/sail bun run build
    ```

## 8. Open the site

Open **https://localhost** (or your own domain).

With sample data you can log in as the owner with `DEFAULT_OWNER_USERNAME` and `DEFAULT_OWNER_PASSWORD` from `.env`, which are `UNIT3D` and `UNIT3D` unless you changed them.


## Everyday commands

```bash
./vendor/bin/sail up -d          # Start
./vendor/bin/sail stop           # Stop, keeping everything
./vendor/bin/sail down           # Remove the containers, keeping the database and certificate
./vendor/bin/sail restart        # Restart
./vendor/bin/sail logs -f        # Follow the logs of all containers
./vendor/bin/sail logs -f queue  # ...or of one service: queue, unit3d-chat, nginx, unit3d-core
./vendor/bin/sail shell          # A shell in the application container
```

> [!CAUTION]
> `./vendor/bin/sail down -v` also deletes the volumes: the **database**, Redis data, the Meilisearch index and the certificate.

After you change `.env`, run `./vendor/bin/sail up -d` again so the containers that use it are recreated.

| Task | Command |
|------|---------|
| Install or update PHP packages | `./vendor/bin/sail composer install` or `composer update` |
| Run migrations | `./vendor/bin/sail artisan migrate` |
| Reset and seed the database | `./vendor/bin/sail artisan migrate:fresh --seed` |
| Install JavaScript packages | `./vendor/bin/sail bun install` |
| Compile assets | `./vendor/bin/sail bun run build` |
| Restart the queue worker after code changes | `./vendor/bin/sail artisan queue:restart` |
| Open a database shell | `./vendor/bin/sail mysql` |
| Open Tinker | `./vendor/bin/sail tinker` |
| Run the tests | `./vendor/bin/sail artisan test` |
| Fix code style | `./vendor/bin/sail pint` |
