# Server Management

<!-- cspell:ignore certbot,chgrp,usermod -->

> [!IMPORTANT]
> The following assumptions are made:
>
> - You have one `root` user and one regular user with sudo privileges on the dedicated server.
> - The regular user with sudo privileges is assumed to have the username `ubuntu`.
> - The project root directory is located at `/var/www/html`.
> - All commands are run from the project root directory.

## 1. Elevated Shell

All SSH and SFTP operations should be conducted using the non-root user. Use `sudo` for any commands that require elevated privileges. Do not use the `root` user directly.

## 2. File Permissions

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

## 3. Handling Code Changes

### PHP Changes

If any PHP files are modified, run the following commands to clear the cache, restart the PHP-FPM service, and restart the Laravel queues:

```sh
sudo php artisan set:all_cache && sudo systemctl restart php8.3-fpm && sudo php artisan queue:restart
```

### Static Assets (SCSS, JS)

If you make changes to SCSS or JavaScript files, rebuild the static assets using:

```sh
bun run build
```

## 4. Changing the Domain

1. **Update the Environment Variables:**

   Modify the domain in the `APP_URL` and `MIX_ECHO_ADDRESS` variables within the `.env` file:

    ```sh
    sudo nano ./.env
    ```

2. **Refresh the TLS Certificate:**

   Use `certbot` to refresh the TLS certificate:

    ```sh
    certbot --redirect --nginx -n --agree-tos --email=sysop@your_domain.tld -d your_domain.tld -d www.your_domain.tld --rsa-key-size 2048
    ```

3. **Update the WebSocket Configuration:**

   Update all domains listed in the WebSocket configuration to reflect the new domain:

    ```sh
    sudo nano ./laravel-echo-server.json
    ```

4. **Restart the Chatbox Server:**

   Reload the Supervisor configuration to apply changes:

    ```sh
    sudo supervisorctl reload
    ```

5. **Compile Static Assets:**

   Rebuild the static assets:

    ```sh
    bun run build
    ```

## 5. Meilisearch Maintenance

Refer [Meilisearch setup for UNIT3D](https://github.com/HDInnovations/UNIT3D-Community-Edition/wiki/Meilisearch-Setup-for-UNIT3D), specifically the [maintenance](https://github.com/HDInnovations/UNIT3D-Community-Edition/wiki/Meilisearch-Setup-for-UNIT3D#3-maintenance) section, for managing upgrades and syncing indexes.

