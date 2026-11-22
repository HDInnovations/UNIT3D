# Server management

<!-- cspell:ignore certbot,chgrp,usermod -->

> [!IMPORTANT]
> The following assumptions are made:
>
> - You have one `root` user and one regular user with sudo privileges on the dedicated server.
> - The regular user with sudo privileges is assumed to have the username `ubuntu`.
> - The project root directory is located at `/var/www/html`.
> - All commands are run from the project root directory.

## 1. Elevated shell

All SSH and SFTP operations should be conducted using the non-root user. Use `sudo` for any commands that require elevated privileges. Do not use the `root` user directly.

## 2. File permissions

Ensure that everything in `/var/www/html` is owned by `www-data:www-data`, except for `node_modules`, which should be owned by `root:root`.

Set up these permissions with the following commands:

```sh
sudo usermod -a -G www-data ubuntu
sudo chown -R www-data:www-data /var/www/html
sudo find /var/www/html -type f -exec chmod 664 {} \;
sudo find /var/www/html -type d -exec chmod 775 {} \;
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
sudo rm -rf node_modules && sudo bun install && sudo bun run build
```

## 3. Handling code changes

### PHP changes

If any PHP files are modified, run the following commands to clear the cache, restart the PHP-FPM service, and restart the Laravel queues:

```sh
sudo php artisan set:all_cache && sudo systemctl restart php8.4-fpm && sudo php artisan queue:restart
```

### Static assets (SCSS, JS)

If you make changes to SCSS or JavaScript files, rebuild the static assets using:

```sh
bun run build
```

## 4. Changing the domain

1. **Update the environment variables:**

   Modify the domain in the `APP_URL` variable within the `.env` file. If you set `VITE_PUSHER_HOST`, update it too:

    ```sh
    sudo nano ./.env
    ```

2. **Refresh the TLS certificate:**

   Use `certbot` to refresh the TLS certificate:

    ```sh
    certbot --redirect --nginx -n --agree-tos --email=sysop@your_domain.tld -d your_domain.tld -d www.your_domain.tld --rsa-key-size 2048
    ```

3. **Update the web server configuration:**

   Make sure the nginx `server_name` (and the TLS certificate from the previous step) use the new domain. The chatbox WebSocket connects through the same domain on `/app/`, so the proxy block does not change. See [Migrating the Chat Server](unit3d_chat_server_migration.md) for it.

4. **Update and restart the chatbox server:**

   Put the new domain in `allowed_origins` of `/etc/zatat/zatat.toml`, restart the chat server, and refresh the cached configuration (this also updates the Content Security Policy):

    ```sh
    sudo nano /etc/zatat/zatat.toml
    sudo systemctl restart unit3d-chat
    sudo php artisan set:all_cache
    ```

5. **Compile static assets:**

   Rebuild the static assets:

    ```sh
    bun run build
    ```

## 5. Meilisearch maintenance

Refer [Meilisearch setup for UNIT3D](https://github.com/HDInnovations/UNIT3D/wiki/Meilisearch-Setup-for-UNIT3D), specifically the [maintenance](https://github.com/HDInnovations/UNIT3D/wiki/Meilisearch-Setup-for-UNIT3D#3-maintenance) section, for managing upgrades and syncing indexes.
