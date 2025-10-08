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
sudo chmod -R ug+rw storage bootstrap/cache
sudo chmod -R 664 storage/views
sudo rm -rf node_modules && bun install && bun run build
```

## 3. Handling code changes

### PHP changes

If any PHP files are modified, run the following commands to clear the cache, restart the PHP-FPM service, and restart the Laravel queues:

```sh
php artisan set:all_cache && sudo systemctl restart php8.3-fpm && php artisan queue:restart
```

### Static assets (SCSS, JS)

If you make changes to SCSS or JavaScript files, rebuild the static assets using:

```sh
bun run build
```

## 4. Changing the domain

1. **Update the environment variables:**

   Modify the domain in the `APP_URL` and `MIX_ECHO_ADDRESS` variables within the `.env` file:

    ```sh
    sudo nano ./.env
    ```

2. **Refresh the TLS certificate:**

   Use `certbot` to refresh the TLS certificate:

    ```sh
    certbot --redirect --nginx -n --agree-tos --email=sysop@your_domain.tld -d your_domain.tld -d www.your_domain.tld --rsa-key-size 2048
    ```

3. **Update the WebSocket configuration:**

   Update all domains listed in the WebSocket configuration to reflect the new domain:

    ```sh
    sudo nano ./laravel-echo-server.json
    ```

4. **Restart the chatbox server:**

   Reload the Supervisor configuration to apply changes:

    ```sh
    sudo supervisorctl reload
    ```

5. **Compile static assets:**

   Rebuild the static assets:

    ```sh
    bun run build
    ```

## 5. Meilisearch maintenance

Refer [Meilisearch setup for UNIT3D](https://github.com/HDInnovations/UNIT3D-Community-Edition/wiki/Meilisearch-Setup-for-UNIT3D), specifically the [maintenance](https://github.com/HDInnovations/UNIT3D-Community-Edition/wiki/Meilisearch-Setup-for-UNIT3D#3-maintenance) section, for managing upgrades and syncing indexes.
