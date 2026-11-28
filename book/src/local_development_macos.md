# Local Development on macOS

_A guide by HDVinnie_

This page covers the macOS-specific parts of the [local development setup](local_development.md): installing the prerequisites and trusting the HTTPS certificate. Follow the main guide for everything else, and come back here for steps 1 and 6.

> [!WARNING]
> This setup is for local development only and is not suitable for production.

## Prerequisites

### Homebrew

If you do not have [Homebrew](https://brew.sh) yet:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Git, PHP and Composer

```bash
brew install git php composer
```

PHP and Composer on your Mac are only used for the first `composer install`. After that, everything runs in Docker.

### Docker Desktop

[Install Docker Desktop](https://docs.docker.com/desktop/install/mac-install/) and launch it. It includes the Compose plugin.

The first start compiles the chat server and mounts your project into containers, which can be slow with the default file sharing. If it is, open `Docker Desktop > Settings > General` and choose **VirtioFS** as the file sharing implementation. If a build is killed for lack of memory, increase the memory limit under `Settings > Resources`.

### Optional: GitHub Desktop and PhpStorm

- [GitHub Desktop](https://desktop.github.com) for cloning. Open <https://github.com/HDInnovations/UNIT3D>, choose `Code > Open with GitHub Desktop`, and pick a local path such as `~/Documents/Personal/UNIT3D`.
- [PhpStorm](https://www.jetbrains.com/phpstorm/): open the project folder with `File > Open`. You can run every command of the main guide from PhpStorm with `Tools > Run Command`, or from its built-in terminal.

Now continue with [step 2 of the main guide](local_development.md#2-clone-the-repository-and-install-php-dependencies).

## Trust the certificate

Do this after `./vendor/bin/sail up -d` has started nginx and you have [copied the certificate](local_development.md#6-trust-the-certificate) to `sail-nginx.pem`.

1. **Add it to the macOS keychain as a trusted certificate:**

    ```bash
    sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain sail-nginx.pem
    ```

    Or double-click `sail-nginx.pem`, open it in Keychain Access, expand `Trust` and set `When using this certificate` to `Always Trust`.

2. **Restart your browser** and open `https://localhost`. Safari and Chrome use the macOS keychain. Firefox has its own certificate store, so in Firefox open `Settings > Privacy & Security > Certificates > View Certificates > Authorities > Import` and import `sail-nginx.pem`.

3. **Delete the copy:**

    ```bash
    rm sail-nginx.pem
    ```

To use a domain such as `unit3d.test`, add it to `/etc/hosts` as described in the [main guide](local_development.md#using-a-domain-other-than-localhost).

Continue with [step 7 of the main guide](local_development.md#7-application-key-database-and-assets).
