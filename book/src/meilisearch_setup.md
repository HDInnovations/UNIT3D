# Meilisearch setup for UNIT3D

**Note:** This guide assumes you are using a `sudo` user named `ubuntu`.

## 1. Install and configure Meilisearch

1. **Install Meilisearch:**

    ```sh
    sudo curl -L https://install.meilisearch.com | sudo sh
    sudo mv ./meilisearch /usr/local/bin/
    sudo chmod +x /usr/local/bin/meilisearch
    ```

2. **Set up directories:**

    ```sh
    sudo mkdir -p /var/lib/meilisearch/data /var/lib/meilisearch/dumps /var/lib/meilisearch/snapshots
    sudo chown -R ubuntu:ubuntu /var/lib/meilisearch
    sudo chmod -R 750 /var/lib/meilisearch
    ```

3. **Generate and record a master key:**

    Generate a 16-byte master key:

    ```sh
    openssl rand -hex 16
    ```

    Record this key, as it will be used in the configuration file.

4. **Configure Meilisearch:**

    ```sh
    sudo curl https://raw.githubusercontent.com/meilisearch/meilisearch/latest/config.toml -o /etc/meilisearch.toml
    sudo nano /etc/meilisearch.toml
    ```

    Update the following in `/etc/meilisearch.toml`:

    ```toml
    env = "production"
    master_key = "your_16_byte_master_key"
    db_path = "/var/lib/meilisearch/data"
    dump_dir = "/var/lib/meilisearch/dumps"
    snapshot_dir = "/var/lib/meilisearch/snapshots"
    ```

5. **Create and enable service:**

    ```sh
    sudo nano /etc/systemd/system/meilisearch.service
    ```

    Add the following:

    ```ini
    [Unit]
    Description=Meilisearch
    After=systemd-user-sessions.service

    [Service]
    Type=simple
    WorkingDirectory=/var/lib/meilisearch
    ExecStart=/usr/local/bin/meilisearch --config-file-path /etc/meilisearch.toml
    User=ubuntu
    Group=ubuntu
    Restart=on-failure

    [Install]
    WantedBy=multi-user.target
    ```

    Enable and start the service:

    ```sh
    sudo systemctl enable meilisearch
    sudo systemctl start meilisearch
    sudo systemctl status meilisearch
    ```

## 2. Configure UNIT3D for Meilisearch

1. **Update `.env`:**

    ```sh
    sudo nano /var/www/html/.env
    ```

    Add the following:

    ```env
    SCOUT_DRIVER=meilisearch
    MEILISEARCH_HOST=http://127.0.0.1:7700
    MEILISEARCH_KEY=your_16_byte_master_key
    ```

2. **Clear configuration and restart services:**

    ```sh
    sudo php artisan set:all_cache
    sudo systemctl restart php8.4-fpm
    sudo php artisan queue:restart
    ```

## 3. Maintenance

1. **Reload data and sync indexes:**

    - **Sync index settings:**

        After UNIT3D updates, sync the index settings to ensure they are up to date:

        ```sh
        sudo php artisan scout:sync-index-settings
        ```

    - **Reload data:**

        Whenever Meilisearch is upgraded or during the initial setup, the database must be reloaded:

        ```sh
        sudo php artisan auto:sync_torrents_to_meilisearch --wipe && sudo php artisan auto:sync_people_to_meilisearch
        ```

## See also

For further details, refer to the [official Meilisearch documentation](https://www.meilisearch.com/docs/guides/deployment/running_production).
