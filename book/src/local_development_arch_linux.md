# Local Development on Arch Linux

<!-- cspell:ignore dockerized,pacman,reqs,certutil,nssdb -->

_A guide by EkoNesLeg_

This page covers the Arch Linux-specific parts of the [local development setup](local_development.md): installing the prerequisites and trusting the HTTPS certificate. Follow the main guide for everything else, and come back here for steps 1 and 6. The commands can be adapted to other Linux distributions.

> [!IMPORTANT]
> This setup is for local development only and is not suitable for production.

## Prerequisites

1. **Install the packages:**

    ```sh
    sudo pacman -S git php composer docker docker-compose
    ```

    See the [Arch Linux Docker documentation](https://wiki.archlinux.org/title/Docker) for details. `docker-compose` provides the `docker compose` plugin that Sail uses.

2. **Start Docker and enable it at boot:**

    ```sh
    sudo systemctl enable --now docker.service
    ```

3. **Run Docker without `sudo`.** Add yourself to the `docker` group, then log out and back in:

    ```sh
    sudo usermod -aG docker "$USER"
    ```

    Do not run Sail with `sudo`. It creates root-owned files that the containers cannot change.

PHP and Composer on your computer are only used for the first `composer install`. After that, everything runs in Docker. If your PHP is older than the version in `composer.json`, use `composer install --ignore-platform-reqs`.

Now continue with [step 2 of the main guide](local_development.md#2-clone-the-repository-and-install-php-dependencies).

## Trust the certificate

Do this after `./vendor/bin/sail up -d` has started nginx and you have [copied the certificate](local_development.md#6-trust-the-certificate) to `sail-nginx.pem`.

1. **Add it to the system certificate store:**

    ```sh
    sudo trust anchor --store sail-nginx.pem
    ```

    To remove it later, run `sudo trust anchor --remove sail-nginx.pem` while the file still exists.

2. **Browsers that use their own certificate store need it too:**

    - **Firefox:** `Settings > Privacy & Security > Certificates > View Certificates > Authorities > Import`, then choose `sail-nginx.pem` and tick `Trust this CA to identify websites`.
    - **Chrome and Chromium** read the NSS database in your home directory. Install the `nss` package and run:

        ```sh
        certutil -d sql:$HOME/.pki/nssdb -A -t "C,," -n unit3d-sail -i sail-nginx.pem
        ```

3. **Restart your browser**, open `https://localhost`, and delete the copy:

    ```sh
    rm sail-nginx.pem
    ```

To use a domain such as `unit3d.test`, add it to `/etc/hosts` as described in the [main guide](local_development.md#using-a-domain-other-than-localhost). If you change the domain later, regenerate the certificate (see [Troubleshooting](local_development.md#troubleshooting)) and trust the new one.

Continue with [step 7 of the main guide](local_development.md#7-application-key-database-and-assets).
