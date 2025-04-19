# Backup

> [!WARNING]
> **Always test backup restoration procedures on a non-critical environment before applying to production.**  
> Incorrect restoration can lead to data loss or service disruption.

| Category       | Severity   | Downtime |
| -------------  |:----------:| ------------:|
| :wrench: Maintenance | Critical | 15 minutes  |

### Introduction

This guide outlines the process of creating and restoring a UNIT3D backup, including decryption with your APP_KEY, file restoration, permission management, and cache reset procedures.

### Built-In Backups

Built-in backups, located in `.../storage/backups/UNT3D`, offer an efficient way to migrate your development codebase to production by leveraging these backups directly. Simply pick one of the three most recent backups, copy it to your home directory, and retrieve your site master key from your `.env` file (the `APP_KEY`). Next, uncompress the backup using `p7zip` (you'll be prompted for the key) and extract any additional ZIP files (typically containing files and a database). Finally, restore the files to your server and manually import the database to ensure your server runs only the committed code.

#### Managing your built-in backup routine

You'll find the built-in backups dashboard link in the Staff dashboard menu or by navigating to `yourSite.tld/dashboard/backups`. This is the front-end management screen to give you a quick view into the status, health, size and, quantity of backups. Across the top bar there are quick options to `Create Full Backup`, `Create Database Backup` and, `Create Files Backup` that enable running un-scheduled backups without the need to log into your server. Using this dashboard helps track whether the options set in the configuration do fulfill the requirements in just a glance.

Located at `.../config/backup.php`, is the configuration file to manage the built-in routine which can be modified to suit your needs.

- **Name**:  The name of this application. You can use this name to monitor the backups.
- **Source**: Set up as an associative array. Within you'll find a 'files' key with child keys detailed below.
- **Include**: The list of directories and files that will be included in the backup.
- **Exclude**: These directories and files will be excluded from the backup. Directories used by the backup process will automatically be excluded.
- **Follow__links**: Determines if symlinks should be followed.
- **Ignore_unreadable_directories**: Determines if it should avoid unreadable folders.
- **Relative_path**: This path is used to make directories in resulting zip-file relative. Set to `null` to include complete absolute path
  - e.g. `base_path()`
- **Databases**: The names of the connections to the databases that should be backed up MySQL, PostgreSQL, SQLite and Mongo databases are supported.
  - The content of the database dump may be customized for each connection by adding a 'dump' key to the connection settings in `.../config/database.php`.
    
    ```php
        'mysql' => [
               ...
              'dump' => [
                   'excludeTables' => [
                        'table_to_exclude_from_backup',
                        'another_table_to_exclude'
                    ]
              ],
          ],
    ```

  - If you are using only InnoDB tables on a MySQL server, you can also supply the useSingleTransaction option to avoid table locking.
    
    ```php
        'mysql' => [
               ...
             'dump' => [
                  'useSingleTransaction' => true,
              ],
        ],
    ```
       
    For a complete list of available customization options, see: https://github.com/spatie/db-dumper
  - **Database_dump_compressor**: The database dump can be compressed to decrease disk space usage. If you do not want any compressor at all, set it to null.
      - Out of the box Laravel-backup supplies: `Spatie\DbDumper\Compressors\GzipCompressor::class`
      - You can also create custom compressor. More info on that here: https://github.com/spatie/db-dumper#using-compression

- **Destination**: Options to modify settings of destination archive/locations
  - **Filename_prefix**: The filename prefix used for the backup zip file.
  - **Disks**: The disk names on which the backups will be stored.

- **Temporary_directory**: The directory where the temporary files will be stored.

- **Notifications**: You can get notified when specific events occur. Out of the box you can use 'mail' and 'slack'.
     - For Slack you need to install laravel/slack-notification-channel.
     - You can also use your own notification classes, just make sure the class is named after one of the `Spatie\Backup\Events` classes.

- **Notifiable**: Here you can specify the notifiable to which the notifications should be sent. The default notifiable will use the variables specified in this config file.
  - The default settings include built-in support for both mail and slack notifications.

- **Monitor_backups**: Here you can specify which backups should be monitored. If a backup does not meet the specified requirements the `UnHealthyBackupWasFound` event will be fired. Here is where you can set both standard time period between backups and the maximum amount of storage to use before considering unhealthy.

- **Cleanup**: The strategy that will be used to cleanup old backups. The default strategy  will keep all backups for a certain amount of days. After that period only a daily backup will be kept. After that period only weekly backups will be kept and so on. No matter how you configure it the default strategy will never delete the newest backup.
  - **Keep_all_backups_for_days**: The number of days for which backups must be kept.
  - **Keep_daily_backups_for_days**: The number of days for which daily backups must be kept.
  - **Keep_weekly_backups_for_weeks**: The number of weeks for which one weekly backup must be kept.
  - **Keep_monthly_backups_for_months**: The number of months for which one monthly backup must be kept.
  - **Keep_yearly_backups_for_years**: The number of years for which one yearly backup must be kept.
  - **Delete_oldest_backups_when_using_more_megabytes_than**: After cleaning up the backups remove the oldest backup until this amount of megabytes has been reached.

- **Security**: This option sets the encryption properties for the outer archive of your backups.
  - **Password**: Set by default to use the `APP_KEY` variable from the `.env` file.
  - **Encryption**: Set by default to use the encryption helper.

---

### Create a Backup
To run a backup of your UNIT3D installation, navigate to your project directory and execute the Artisan backup command:


```bash
cd /var/www/html
php artisan backup:run
```

### Viewing Backup List

To view available backups, run:

```bash
cd /var/www/html
php artisan backup:list
```

---

## Tutorial Wrap-Up

- **Install Required Tools:** Set up 7-Zip and other utilities.
- **Retrieve Your Application Key:** Extract your `APP_KEY` from the `.env` file.
- **Uncompress the Backup:** Use 7-Zip to decrypt and extract your UNIT3D backup files.
- **Restore Files:** Copy the restored files to your live environment using proper commands.
- **Fix File Permissions:** Ensure correct ownership and permissions with `chown`, `chmod`, and related commands.
- **Reset Caches:** Clear caches and restart PHP Artisan and PHP-FPM to finalize restoration.

_This tutorial is crafted for anyone looking to restore a backup on UNIT3D, manage backup recovery for a UNIT3D private tracker, or implement PHP Laravel backup restoration. If you've been searching for solutions like "restore backup UNIT3D," "PHP Laravel backup restoration," "UNIT3D private tracker restore," or "backup recovery for UNIT3D," this guide offers practical and detailed instructions to help you._

---

## Index

- [Prerequisites](#prerequisites)
- [Step 1: Install Required Tools](#step-1-install-required-tools)
- [Step 2: Retrieve Your Application Key](#step-2-retrieve-your-application-key)
- [Step 3: Uncompress the Backup](#step-3-uncompress-the-backup)
- [Step 4: Restore the Files](#step-4-restore-the-files)
- [Step 5: Fix File Permissions](#step-5-fix-file-permissions)
- [Step 6: Reset PHP Artisan and PHP-FPM](#step-6-reset-php-artisan-and-php-fpm)
- [Troubleshooting](#troubleshooting)

---

## Prerequisites

- **User Privileges:** Sudo access is required for installing packages and changing file permissions.
- **Tools:**  
  - [7-Zip](https://www.7-zip.org/) (for extracting encrypted backups and handling ZIP files)  
  - A text editor (e.g., `nano` or `micro`)
- **Backup File:** A UNIT3D backup file (for example: `[UNIT3D]2025-03-05-00-00-51.zip`) stored in your UNIT3D backup folder.
- **App Key:** You need your `APP_KEY` from the `.env` file, which is used as the decryption password when extracting the backup.

> **Note:** This tutorial was tested on **UNIT3D v9.0.1** running on **PHP 8.4**. If you are using a different version, some commands and steps may require adjustments.


---

## Step 1: Install Required Tools

First, ensure you have the tools needed to extract the backup.

```bash
cd ~
sudo apt update
sudo apt install p7zip-full -y
```

p7zip-full is used for handling compatibility and encryption issues when uncompressing the backup archives.


---

## Step 2: Retrieve Your Application Key
Your backup file is encrypted with your `APP_KEY`. Open the .env file to locate it.

```bash
sudo nano /var/www/html/.env
```

Copy the `APP_KEY` value (look for line starting with 'APP_KEY=')

or

Retrieve key from environment:

```bash
sudo grep 'APP_KEY=' /var/www/html/.env
```

You will use this key when extracting the backup.

---

## Step 3: Uncompress the Backup

### 1. Create a Temporary Directory
Create a temporary directory to work in and navigate into it:

```bash
mkdir ~/tempBackup
cd ~/tempBackup
```

### 2. Copy the Backup File
Copy your backup file from its location (e.g., `.../storage/backups/UNT3D`) to the temporary directory. Adjust the file name and path as needed:

```bash
cp /var/www/html/storage/backups/UNIT3D/\[UNIT3D\]2025-03-22-17-29-59.zip ./
```

Tip: Instead of typing the full file name, copy and paste the directory path (e.g., `.../backups/UNIT3D/\[UNIT3D`), then press the Tab key to auto-complete the backup file name:

```bash
cp /var/www/html/storage/backups/UNIT3D/\[UNIT3D
```
> [!NOTE]
> Note: Remember to add ./ at the end of the command to specify the current directory as the destination.
> 
For example: `cp /var/www/html/storage/backups/UNIT3D/\[UNIT3D\]2025-03-22-17-29-59.zip ./`



### 3. Extract the Backup File Using 7z
Use `7z` to extract the encrypted backup file. When prompted, enter your `APP_KEY`. The password prompt will not echo your input:

```bash
7z x \[UNIT3D\]2025-03-22-17-29-59.zip
```

#### Example: APP_KEY Usage
If your `.env` file contains:

```bash
APP_KEY=base64:bT70DC4Ck7taYqP6ugqKIYbAbiEFbgECSdc03MwtXg=
```

When prompted during extraction, enter the password (note: your input will not be echoed):

```bash
base64:bT70DC4Ck7taYqP6ugqKIYbAbiEFbgECSdc03MwtXg=
```

### 4. Unzip the Extracted Archive
If the extraction process produces a standard ZIP file (e.g., `backup.zip`), extract it with:

```bash
7z x backup.zip
```

---

**Explanation:**  
- The first `7z` command decrypts and extracts the outer backup archive using your `APP_KEY`.  
- The second `7z` command handles the inner ZIP file to further extract the backup contents.


---

## Step 4: Restore the Files
After extraction, your backup files will be available in the temporary folder. Now, copy them back to your web server directory.

Copy the Entire html Folder:

```bash
sudo cp -rv ~/tempBackup/var/www/html /var/www
```

Alternatively, Depending on Your Setup, You Might Need to Copy at a Higher Level:

```bash
sudo cp -rv ~/tempBackup/var/www /var
```

Or Restore Specific Files:

```bash
sudo -u www-data cp -rf ./var/www/html/someDirectory/someFile.php /var/www/html/someDirectory/someFile.php
```

Tip:
Choose the command that best fits your restoration needs. If you only need to restore specific files, copy only those.

---


## Step 4.1: Alternate Restoration Method Using entire /var/www folder! 

If you prefer an alternative method, you can create a zipped backup of your current www folder, then restore it from the zip archive. Follow these steps:

---
> [!NOTE]
> Be careful, the steps below are an alternative method for backing up the entire ```/var/www``` folder! Only continue if you understand what you are doing.


---

If you haven't already created a temporary backup directory, run:

```bash
mkdir ~/tempBackup
```


### 1. Create a Zip Archive of the www Folder
First, create a zipped archive of your entire `/var/www` folder and save it to your temporary backup directory:

```bash
sudo 7za a -tzip -r ~/tempBackup/www_backup_$(date +%Y%m%d%H%M%S).zip /var/www
```

This command compresses your entire `/var/www` folder into a zip file and saves it in your temporary backup directory (`~/tempBackup`).

The file will be named with a timestamp for reference. For example: `www_backup_20250322062714.zip`

### 2. Unzip the Archive into a New Restoration Folder

Create a new folder (e.g., restore_www_TIMESTAMP) in your temporary backup directory and unzip the archive into it:

The folder should be named following the format restore_www_TIMESTAMP, where TIMESTAMP corresponds to the timestamp in your backup file.

> [!NOTE]
> **Important:** Replace `$(date +%Y%m%d%H%M%S)` with the actual timestamp from your backup file.
> 
> For example, if your backup file is named `www_backup_20250322062714.zip`, then set `TIMESTAMP=20250322062714`.


```bash
TIMESTAMP=$(date +%Y%m%d%H%M%S)
mkdir ~/tempBackup/restore_www_$TIMESTAMP
7z x ~/tempBackup/www_backup_$TIMESTAMP.zip -d ~/tempBackup/restore_www_$TIMESTAMP
```

> Ensure that the same timestamp is used for both creating and unzipping the archive. Alternatively, you can manually specify a consistent name.

### 3. Copy the Restored Files to the Correct Location
Once you have verified the restored files in the new folder, copy the entire contents back to your live directory:

```bash
TIMESTAMP=$(date +%Y%m%d%H%M%S)
sudo cp -a ~/tempBackup/restore_www_$TIMESTAMP/var/www/html/. /var/www/html/
```

_The -a flag preserves file permissions, ownership, and timestamps._


> [!NOTE]
> **Restoration Command Template**
> ```bash
> sudo cp -a ~/tempBackup/restore_www_$TIMESTAMP/var/www/html/. /var/www/html/
> ```
> 
> **Key Parameters** (adjust accordingly):
> - `$TIMESTAMP`: Timestamp from your backup filename (e.g., `20250323064937`).
> The folder `/var/www/html/` is used as the default project folder in this documentation.
>
> If you want to backup a different folder, simply replace `/var/www/html/` with the path to your desired folder in all the commands.
> 
> **Example for /html restoration**:
> ```bash
> sudo cp -a ~/tempBackup/restore_www_20250323064937/var/www/html/. /var/www/html/
> ```
> 
> Before applying any commands, double-check that your backup file contains the correct file structure (e.g., `/var/www/html/` in your backup ZIP) and that your live folder (e.g., `your-target-dir/`) matches your intended deployment path.
> 
> **Tip**: Preview structure first:
> ```bash
> 7z -l www_backup_$TIMESTAMP.zip | grep "var/www/html"
> ```




---

## Step 5: Fix File Permissions
Once the files are restored, adjust the file permissions to ensure your web server can access them correctly.

```bash
cd /var/www/html
sudo chown -R www-data:www-data /var/www/html
sudo find /var/www/html -type f -exec chmod 664 {} \;
sudo find /var/www/html -type d -exec chmod 775 {} \;
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

Explanation:

_chown changes the owner to www-data, which is typically the web server user.
chmod commands set the proper permissions: files are set to 664 and directories to 775.
The additional commands ensure that the storage and bootstrap/cache directories have the correct group and permissions for writing._

### 5.1 Optional: Reinstall Dependencies and Rebuild Assets

If reinstalling Node.js dependencies and running a build process is part of your backup recovery, then execute the command below.

Run this only if you do not have a valid node_modules folder or if your assets require rebuilding.

Note: Running this command with sudo may cause permission issues (such as EACCES errors) so if possible consider running it as a non-root user or ensure that the permissions are correctly set after installation.

When to Use:
Use the optional command if your project uses Bun for dependency management and asset building and if you need to ensure that your dependencies and built assets are updated. 

> [!NOTE]
> If your backup already contains a valid node_modules folder with pre-built assets, this step can be skipped to avoid potential permission issues.

Optional Rebuild Command:

```bash
sudo rm -rf node_modules && sudo bun install && sudo bun run build
```

sudo rm -rf node_modules: Removes the current Node.js dependencies to allow a fresh install.
sudo bun install: Reinstalls all dependencies as specified in your project’s configuration.
sudo bun run build: Runs the build process to compile or bundle assets as needed.

---

## Step 6: Reset PHP Artisan and PHP-FPM
Finally, reset caches and queues with PHP Artisan and restart your PHP-FPM service.

```bash
cd /var/www/html
sudo php artisan set:all_cache && sudo systemctl restart php8.4-fpm && sudo php artisan queue:restart
```

Explanation:

_php artisan set:all_cache clears and rebuilds your application's cache.
Restarting PHP-FPM ensures that any changes are recognized by the PHP process.
Restarting the queue allows any queued jobs to continue without issues._


## Troubleshooting

Below are some common issues and their suggested solutions:

| **Symptom**                | **Solution**                                              |
|----------------------------|-----------------------------------------------------------|
| 500 Internal Server Error  | Run `php artisan optimize:clear`                          |
| Database Connection Issues | Verify your `.env` credentials                            |
| Missing Files              | Re-run `rsync` with the `--checksum` option               |
| Permission Denied          | Reapply ACL permissions                                   |
| Queue Workers Inactive     | Restart all workers with `sudo supervisorctl restart all` |





