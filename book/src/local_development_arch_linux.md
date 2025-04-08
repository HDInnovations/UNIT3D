# UNIT3D v8.x.x on Arch Linux with Laravel Sail

<!-- cspell:ignore dockerized,pacman -->

_A guide by EkoNesLeg_

This guide outlines the steps to set up UNIT3D using Laravel Sail on Arch Linux. While the guide highlights the use of Arch Linux, the instructions can be adapted to other environments.

> [!IMPORTANT]
> This guide is intended for local development environments only and is not suitable for production deployment.

## Modifying `.env` and Secure Headers for Non-HTTPS Instances

For local development, HTTP is commonly used instead of HTTPS. To prevent mixed content issues, adjust your `.env` file as follows:

1. **Modify the `.env` Config:**
    - Open your `.env` file in the root directory of your UNIT3D project.
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

Ensure Docker and Docker Compose are installed, as they are required for managing the Dockerized development environment provided by Laravel Sail.

### Installing Docker and Docker Compose

Refer to the [Arch Linux Docker documentation](https://wiki.archlinux.org/title/Docker) and install the necessary packages:

```sh
sudo pacman -S docker docker-compose
```

## Step 1: Clone the Repository

Clone the UNIT3D repository to your local environment:

1. Navigate to your chosen workspace directory:

    ```sh
    cd ~/PhpstormProjects
    ```

2. Clone the repository:

    ```sh
    git clone git@github.com:HDInnovations/UNIT3D-Community-Edition.git
    ```

## Step 2: Composer Dependency Installation

1. **Change to the project’s root directory:**

    ```sh
    cd ~/PhpstormProjects/UNIT3D-Community-Edition
    ```

2. **Install Composer dependencies:**

    Run the following command to install the PHP dependencies:

    ```sh
    composer install
    ```

## Step 3: Docker Environment Initialization

1. **Switch to Branch 8.x.x:**

    Before starting Docker, switch to the `8.x.x` branch:

    ```sh
    git checkout 8.x.x
    ```

2. **Start the Docker environment using Laravel Sail:**

    ```sh
    ./vendor/bin/sail up -d
    ```

## Step 4: App Key Generation

Generate a new `APP_KEY` in the `.env` file for encryption:

```bash
./vendor/bin/sail artisan key:generate
```

**Note**: If you are importing a database backup, make sure to set the `APP_KEY` in the `.env` file to match the key used when the backup was created.

## Step 5: Database Migrations and Seeders

Initialize your database with sample data by running migrations and seeders:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

> [!IMPORTANT]
> This operation resets your database and seeds it with default data. Avoid running this in a production environment.

## Step 6: Database Preparation

### Initial Database Loading

Prepare your database with the initial schema and data. Make sure you have a database dump file, such as `prod-site-backup.sql`.

### MySQL Data Importation

Import your database dump into MySQL within the Docker environment:

```bash
./vendor/bin/sail mysql -u root -p unit3d < prod-site-backup.sql
```

**Note**: Ensure that the `APP_KEY` in the `.env` file matches the key used in your deployment environment for compatibility.

## Step 7: NPM Dependency Management

Manage Node.js dependencies and compile assets within the Docker environment:

```bash
./vendor/bin/sail bun install
./vendor/bin/sail bun run build
```

If needed, refresh the Node.js environment:

```bash
./vendor/bin/sail rm -rf node_modules && bun pm cache rm && bun install && bun run build
```

## Step 8: Application Cache Configuration

Optimize the application's performance by setting up the cache:

```bash
./vendor/bin/sail artisan set:all_cache
```

## Step 9: Environment Restart

Apply new configurations or restart the environment by toggling the Docker environment:

```bash
./vendor/bin/sail restart && ./vendor/bin/sail artisan queue:restart
```

## Additional Notes

- **Permissions**: Use `sudo` cautiously to avoid permission conflicts, particularly with Docker commands that require elevated access.

### Appendix: Sail Commands for UNIT3D

This section provides a reference for managing and interacting with UNIT3D using Laravel Sail.

#### Docker Management

- **Start Environment**:
  ```bash
  ./vendor/bin/sail up -d
  ```
  Starts Docker containers in detached mode.

- **Stop Environment**:
  ```bash
  ./vendor/bin/sail down
  ```
  Stops and removes Docker containers.

- **Restart Environment**:
  ```bash
  ./vendor/bin/sail restart
  ```
  Applies changes by restarting the Docker environment.

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
  Seeds the database with predefined data.

- **Refresh Database**:
  ```bash
  ./vendor/bin/sail artisan migrate:fresh --seed
  ```
  Resets and seeds the database.

- **Cache Configurations**:
  ```bash
  ./vendor/bin/sail artisan set:all_cache
  ```
  Clears and caches configurations for performance.

#### NPM and Assets

- **Install NPM Dependencies**:
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

- **Run PHPUnit (PEST) Tests**:
  ```bash
  ./vendor/bin/sail artisan test
  ```
  Runs PEST tests for the application.
