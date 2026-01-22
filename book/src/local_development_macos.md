# UNIT3D v8.x.x on MacOS with Laravel Sail and PhpStorm

_A guide by HDVinnie_

This guide is designed for setting up UNIT3D, a Laravel application, leveraging Laravel Sail on MacOS.

**Warning**: This setup guide is intended for local development environments only and is not suitable for production
deployment.

## Modifying .env and secure headers for non-HTTPS instances

For local development, HTTP is commonly used instead of HTTPS. To prevent mixed content issues, adjust your `.env` file as follows:

1. **Create the `.env` Config:**
    - Create a `.env` file in the root directory of your UNIT3D project.
    - Copy and paste the contents from `.env.example` into the `.env` file.
    - Add or modify the following environment variables:

        ```dotenv
        DB_HOST=mysql               # Match the container name in the compose file
        DB_USERNAME=unit3d          # The username can be anything except `root`
        SESSION_SECURE_COOKIE=false # Disables secure cookies
        REDIS_HOST=redis            # Match the container name in the compose file
        CSP_ENABLED=false           # Disables Content Security Policy
        HSTS_ENABLED=false          # Disables Strict Transport Security
        ```

## Prerequisites

### Installing Homebrew

If you don't have Homebrew installed:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Installing PHP and Composer

```bash
brew install php composer
```

### Installing Docker Desktop

[Install Docker Desktop](https://docs.docker.com/desktop/install/mac-install/)

Once installed, launch Docker Desktop

### Installing GitHub Desktop

[Install GitHub Desktop](https://desktop.github.com)

Once installed, launch GitHub Desktop

### Installing PHPStorm

[Install PHPStorm](https://www.jetbrains.com/phpstorm/)

Once installed, launch PHPStorm

## Step 1: clone the repository

Firstly, clone the UNIT3D repository to your local environment by visiting [UNIT3D Repo](https://github.com/HDInnovations/UNIT3D). Then click the blue colored code button and select `Open with Github Desktop`. Once Github Desktop is open set you local path to clone to like `/Users/HDVinnie/Documents/Personal/UNIT3D`

## Step 2: open the project in PHPStorm

Within PHPStorm goto `File` and then click `Open`. Select the local path you just did like `/Users/HDVinnie/Documents/Personal/UNIT3D`.

### The following commands are run in PHPStorm. Can do so by clicking `Tools->Run Command`.

## Step 2: Environment configuration

1. **Create the `.env` file:**
   ```bash
   cp .env.example .env
   ```

2. **Configure Docker-specific settings:**
   Edit your `.env` file and ensure these Docker container settings are configured:
   ```dotenv
   DB_HOST=mysql               # Match the container name in the compose file
   DB_USERNAME=unit3d          # The username can be anything except `root`
   REDIS_HOST=redis            # Match the container name in the compose file
   ```

## Step 3: Composer dependency installation

Install PHP dependencies to bootstrap Laravel Sail:

```bash
composer install
```

**Note**: This step is required before using Laravel Sail because `./vendor/bin/sail` doesn't exist until Composer installs the Laravel Sail package and creates the vendor directory.

## Step 4: start Sail
Initialize the Docker environment using Laravel Sail:

```bash
./vendor/bin/sail up -d
```

## Step 5: app key generation

Generate a new `APP_KEY` in the `.env` file for encryption:

```bash
./vendor/bin/sail artisan key:generate
```

**Note**: If you are importing a database backup, make sure to set the `APP_KEY` in the `.env` file to match the key used when the backup was created.

## Step 6: Bun dependency install and compile assets

```bash
./vendor/bin/sail bun install
```

```bash
./vendor/bin/sail bun run build
```

## Step 7: Database setup

Choose one of the following options:

### Step 7a: Database migrations and seeders (for sample data)

For database initialization with sample data, apply migrations and seeders:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

**Caution**: This operation will reset your database and seed it with default data. Exercise caution in production
settings.

### Step 7b: Database preparation (for production database backup)

If you want to use a production database backup locally:

### Initial database loading

Prepare your database with the initial schema and data. Ensure you have a database dump file,
e.g., `prod-site-backup.sql`.

### MySQL data importation

To import your database dump into MySQL within the local environment, use:

```bash
./vendor/bin/sail mysql -u root -p unit3d < prod-site-backup.sql
```

**Note**: For this to work properly you must set the APP_KEY value in your local `.env` file to match you prod APP_KEY value.

## Step 8: application cache configuration

Optimize the application's performance by setting up the cache:

```bash
./vendor/bin/sail artisan set:all_cache
```

## Step 9: visit local instance

Open your browser and visit `localhost`. Enjoy!

## Additional notes

- **Permissions**: Exercise caution with `sudo` to avoid permission conflicts, particularly for Docker commands
  requiring elevated access.

### Appendix: Sail commands for UNIT3D

This section outlines commands for managing and interacting with UNIT3D using Laravel Sail.

#### Sail management

- **Start environment**:
  ```bash
  ./vendor/bin/sail up -d
  ```
  Starts Docker containers in detached mode.

- **Stop environment**:
  ```bash
  ./vendor/bin/sail down -v
  ```
  Stops and removes Docker containers.

- **Restart environment**:
  ```bash
  ./vendor/bin/sail restart
  ```
  Applies changes by restarting Docker environment.

#### Dependency management

- **Install Composer dependencies**:
  ```bash
  ./vendor/bin/sail composer install
  ```
  Installs PHP dependencies defined in `composer.json`.

- **Update Composer dependencies**:
  ```bash
  ./vendor/bin/sail composer update
  ```
  Updates PHP dependencies defined in `composer.json`.

#### Laravel Artisan

- **Run migrations**:
  ```bash
  ./vendor/bin/sail artisan migrate
  ```
  Executes database migrations.

- **Seed database**:
  ```bash
  ./vendor/bin/sail artisan db:seed
  ```
  Seeds database with predefined data.

- **Refresh database**:
  ```bash
  ./vendor/bin/sail artisan migrate:fresh --seed
  ```
  Resets and seeds database.

- **Cache configurations**:
  ```bash
  ./vendor/bin/sail artisan set:all_cache
  ```
  Clears and caches configurations for performance.

#### NPM and assets

- **Install Bun dependencies**:
  ```bash
  ./vendor/bin/sail bun install
  ```
  Installs Node.js dependencies.

- **Compile assets**:
  ```bash
  ./vendor/bin/sail bun run build
  ```
  Compiles CSS and JavaScript assets.

#### Database operations

- **MySQL interaction**:
  ```bash
  ./vendor/bin/sail mysql -u root -p
  ```
  Opens MySQL CLI for database interaction.

#### Queue management

- **Restart queue workers**:
  ```bash
  ./vendor/bin/sail artisan queue:restart
  ```
  Restarts queue workers after changes.

#### Troubleshooting

- **View logs**:
  ```bash
  ./vendor/bin/sail logs
  ```
  Displays Docker container logs.

- **PHPUnit (PEST) tests**:
  ```bash
  ./vendor/bin/sail artisan test
  ```
  Runs PEST tests for application.
