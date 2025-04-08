# UNIT3D v8.x.x on MacOS with Laravel Sail and PhpStorm

_A guide by HDVinnie_

This guide is designed for setting up UNIT3D, a Laravel application, leveraging Laravel Sail on MacOS.

**Warning**: This setup guide is intended for local development environments only and is not suitable for production
deployment.

## Modifying .env and Secure Headers for Non-HTTPS Instances

For local development, it's common to use HTTP instead of HTTPS. To prevent mixed content issues, follow these steps:

1. **Modify the `.env` Config:**
    - Open your `.env` file located in the root directory of your UNIT3D project.
    - Find the `SESSION_SECURE_COOKIE` setting and change its value to `false`. This action disables secure cookies,
      which are otherwise required for HTTPS.

    ```dotenv
    SESSION_SECURE_COOKIE=false
    ```

2. **Adjust the Secure Headers in `config/secure-headers.php`:**
    - Navigate to the `config` directory and open the `secure-headers.php` file.
    - To disable the `Strict-Transport-Security` header, locate the `hsts` setting and change its value to `false`.

    ```php
    'hsts' => false,
    ```

    - Next, locate the Content Security Policy (CSP) configuration to adjust it for HTTP. Disable the CSP to prevent it
      from blocking content that doesn't meet the HTTPS security requirements.

    ```php
    'enable' => env('CSP_ENABLED', false),
    ```

## Prerequisites

### Installing Docker Desktop

[Install Docker Desktop](https://docs.docker.com/desktop/install/mac-install/)

Once installed, launch Docker Desktop

### Installing GitHub Desktop

[Install GitHub Desktop](https://desktop.github.com)

Once installed, launch GitHub Desktop

### Installing PHPStorm

[Install PHPStorm](https://www.jetbrains.com/phpstorm/)

Once installed, launch PHPStorm

## Step 1: Clone the Repository

Firstly, clone the UNIT3D repository to your local environment by visiting [UNIT3D-Community-Edition Repo](https://github.com/HDInnovations/UNIT3D-Community-Edition). Then click the blue colored code button and select `Open with Github Desktop`. Once Github Desktop is open set you local path to clone to like `/Users/HDVinnie/Documents/Personal/UNIT3D-Community-Edition`

## Step 2: Open The Project In PHPStorm

Within PHPStorm goto `File` and then click `Open`. Select the local path you just did like `/Users/HDVinnie/Documents/Personal/UNIT3D-Community-Edition`.

### The following commands are run in PHPStorm. Can do so by clicking `Tools->Run Command`.

## Step 3: Start Sail
Initialize the Docker environment using Laravel Sail:

```bash
./vendor/bin/sail up -d
```

## Step 4: Composer Dependency Installation

```bash
./vendor/bin/sail composer install
```

## Step 5: Bun Dependency Install and Compile Assets

```bash
./vendor/bin/sail bun install
```

```bash
./vendor/bin/sail bun run build
```

## Step 6: Database Migrations and Seeders

For database initialization with sample data, apply migrations and seeders:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

**Caution**: This operation will reset your database and seed it with default data. Exercise caution in production
settings.

## Step 7: Database Preparation (If want to use a production database backup locally)

### Initial Database Loading

Prepare your database with the initial schema and data. Ensure you have a database dump file,
e.g., `prod-site-backup.sql`.

### MySQL Data Importation

To import your database dump into MySQL within the local environment, use:

```bash
./vendor/bin/sail mysql -u root -p unit3d < prod-site-backup.sql
```

**Note**: For this to work properly you must set the APP_KEY value in your local `.env` file to match you prod APP_KEY value.

## Step 8: Application Cache Configuration

Optimize the application's performance by setting up the cache:

```bash
sail artisan set:all_cache
```

## Step 9: Visit Local Instance

Open your browser and visit `localhost`. Enjoy!

## Additional Notes

- **Permissions**: Exercise caution with `sudo` to avoid permission conflicts, particularly for Docker commands
  requiring elevated access.

### Appendix: Sail Commands for UNIT3D

This section outlines commands for managing and interacting with UNIT3D using Laravel Sail.

#### Sail Management

- **Start Environment**:
  ```bash
  ./vendor/bin/sail up -d
  ```
  Starts Docker containers in detached mode.

- **Stop Environment**:
  ```bash
  ./vendor/bin/sail down -v
  ```
  Stops and removes Docker containers.

- **Restart Environment**:
  ```bash
  ./vendor/bin/sail restart
  ```
  Applies changes by restarting Docker environment.

#### Dependency Management

- **Install Composer Dependencies**:
  ```bash
  ./vendor/bin/sail composer install
  ```
  Installs PHP dependencies defined in `composer.json`.

- **Update Composer Dependencies**:
  ```bash
  ./vendor/bin/sail composer update
  ```
  Updates PHP dependencies defined in `composer.json`.

#### Laravel Artisan

- **Run Migrations**:
  ```bash
  ./vendor/bin/sail artisan migrate
  ```
  Executes database migrations.

- **Seed Database**:
  ```bash
  ./vendor/bin/sail artisan db:seed
  ```
  Seeds database with predefined data.

- **Refresh Database**:
  ```bash
  ./vendor/bin/sail artisan migrate:fresh --seed
  ```
  Resets and seeds database.

- **Cache Configurations**:
  ```bash
  ./vendor/bin/sail artisan set:all_cache
  ```
  Clears and caches configurations for performance.

#### NPM and Assets

- **Install Bun Dependencies**:
  ```bash
  ./vendor/bin/sail bun install
  ```
  Installs Node.js dependencies.

- **Compile Assets**:
  ```bash
  ./vendor/bin/sail bun run build
  ```
  Compiles CSS and JavaScript assets.

#### Database Operations

- **MySQL Interaction**:
  ```bash
  ./vendor/bin/sail mysql -u root -p
  ```
  Opens MySQL CLI for database interaction.

#### Queue Management

- **Restart Queue Workers**:
  ```bash
  ./vendor/bin/sail artisan queue:restart
  ```
  Restarts queue workers after changes.

#### Troubleshooting

- **View Logs**:
  ```bash
  ./vendor/bin/sail logs
  ```
  Displays Docker container logs.

- **PHPUnit (PEST) Tests**:
  ```bash
  ./vendor/bin/sail artisan test
  ```
  Runs PEST tests for application.