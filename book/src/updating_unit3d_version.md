# Updating UNIT3D

Update UNIT3D and its dependencies following the steps below.

## 1. Create Backup

UNIT3D offers built-in backups. Refer to the Backups documentation for usage.

> [!IMPORTANT]   
> Ensure you have a complete backup before proceeding.

## 2. Enter Maintenance Mode

```bash
cd /var/www/html
php artisan down
```

## 3. Update Meilisearch

- **Stop** the service and install the latest Meilisearch:

```bash
sudo apt update
sudo apt upgrade
sudo systemctl stop meilisearch
sudo curl -L https://install.meilisearch.com | sudo sh
sudo mv ./meilisearch /usr/local/bin/
sudo chmod +x /usr/local/bin/meilisearch
sudo rm -rf /var/lib/meilisearch/data
```

- **Restart** Meilisearch:

```bash
sudo chmod +x /usr/local/bin/meilisearch
sudo systemctl start meilisearch
```

## 4. Update PHP

- **Refer** to the **Upgrading PHP Version** documentation.


## 5. Update UNIT3D

- **Proceed** to update UNIT3D after completing the PHP upgrade steps:

```bash
cd /var/www/html
php artisan git:update
```

During the update, UNIT3D prompts for action on file differences. Copy the latest backup to `~/tempBackup` before starting. After the update, review `~/tempBackup/fileConflicts.txt` for conflicts.

## 6. Database Migration Fix

During the update, an error related to the `tickets` table may occur:

```sql
2025_02_17_074140_update_columns_to_boolean ......................................................................................... 38.50ms FAIL

In Connection.php line 825:                                                                                                              
  SQLSTATE[22004]: Null value not allowed: 1138 Invalid use of NULL value (Connection: mysql, SQL: alter table `tickets` modify `staff_read` tinyint(1) not null default '0')                                                                                                                 

In Connection.php line 571:                     
  SQLSTATE[22004]: Null value not allowed: 1138 Invalid use of NULL value
```

**Resolve** `Null` values in `tickets` table:

1. Log in to MySQL:

```bash
mysql -u your_username -p
```

**Enter** the MySQL password when prompted. After logging in, select the appropriate database by running:

```bash
USE your_database_name;
```

2. Fix the Null Values:

**Update** `NULL` entries in the `staff_read` column to `0` by running the following SQL command:

```sql
UPDATE tickets SET staff_read = 0 WHERE staff_read IS NULL;
```

3. Exit MySQL:

```sql
exit;
```

4. Complete migrations:

```bash
php artisan migrate
```

---

## 7. Final Reset & Cleanup

- Clear caches, reinstall dependencies, rebuild assets, and restart services after updating and migrating.

```bash
sudo -u www-data composer install --prefer-dist --no-dev -o && \
sudo php artisan cache:clear && \
sudo php artisan queue:clear && \
sudo php artisan auto:email-blacklist-update && \
sudo php artisan auto:cache_random_media && \
sudo php artisan set:all_cache && \
bun install && \
bun run build && \
sudo php artisan migrate && \
sudo systemctl restart php8.4-fpm && \
sudo php artisan queue:restart && \
sudo supervisorctl reread && \
sudo supervisorctl update && \
sudo supervisorctl reload && \
sudo php artisan scout:sync-index-settings && \
sudo php artisan auto:sync_torrents_to_meilisearch --wipe && \
sudo php artisan auto:sync_people_to_meilisearch
```

- Resume normal site functionality:

```bash
sudo php artisan scout:sync-index-settings && \
sudo php artisan auto:sync_torrents_to_meilisearch --wipe && \
sudo php artisan auto:sync_people_to_meilisearch && \
sudo php artisan set:all_cache && \
sudo systemctl restart php8.4-fpm && \
sudo php artisan queue:restart && \
sudo php artisan up
```
