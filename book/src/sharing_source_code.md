# UNIT3D Open Source: How to Share Your Source Code

_A guide by EkoNesLeg_

## 1. Introduction

As part of complying with the [GNU Affero General Public License (AGPL)](https://github.com/HDInnovations/UNIT3D-Community-Edition/blob/master/LICENSE), sites that modify and distribute UNIT3D are required to share their source code. This guide provides an easy process for creating a sanitized tarball of your modified source code and encourages you to create and update an "Open Source" page on your site to make this code available.

## 2. Setting Up Tarball Creation

### 2.1 Exclude Sensitive Files

To create a tarball that includes only the modified source code and excludes sensitive files like configuration data, you can take advantage of the existing `.gitignore` file in your UNIT3D deployment. Here’s how:

1. **Reference `.gitignore` for Exclusions:**

   If your production environment has the original `.gitignore` file that already lists the files and directories you don’t want to include in version control, you can use it to exclude those same items from your tarball:

   ```sh
   ln -s /var/www/html/.gitignore /var/www/html/.tarball_exclude
   ```

2. **Additional Exclusions (if needed):**

   If additional exclusions are needed, or if you've removed the git environment from your production environment, you should manually add the exclusions to the `.tarball_exclude` file:

   ```sh
   nano /var/www/html/.tarball_exclude
   ```

   Add the following to the file:

<!-- cspell:disable -->
   ```plaintext
   .env
   node_modules
   storage
   vendor
   public
   *.gz
   *.lock
   UNIT3D-Announce
   unit3d-announce
   laravel-echo-server.json
   config
   .DS_Store
   .idea
   .vscode
   nbproject
   .phpunit.cache
   .ftpconfig
   storage/backups
   storage/debugbar
   storage/gitupdate
   storage/*.key
   laravel-echo-server.lock
   .vagrant
   Homestead.json
   Homestead.yaml
   npm-debug.log
   _ide_helper.php
   supervisor.ini
   .phpunit.cache/
   .phpstan.cache/
   caddy
   frankenphp
   frankenphp-worker.php
   data
   config/caddy/autosave.json
   build
   bootstrap
   *.sql
   *.DS_Storecomposer.lock
   *.swp
   coverage.xml
   cghooks.lock
   *.pyc
   emojipy-*
   emojipy.egg-info/
   lib/js/tests.html
   lib/js/tests/npm-debug.log
   ```
<!-- cspell:enable -->

### 2.2 Create the Tarball

1. **Create a script to generate the tarball:**

    ```sh
    nano /var/www/html/create_tarball.sh
    ```

2. **Add the following content to the script:**

    ```sh
    #!/bin/bash

    TARBALL_NAME="UNIT3D_Source_$(date +%Y%m%d_%H%M%S).tar.gz"
    TAR_EXCLUDES="--exclude-from=/var/www/html/.tarball_exclude"

    tar $TAR_EXCLUDES -czf /var/www/html/public/$TARBALL_NAME -C /var/www html

    # Create a symlink to the latest tarball
    ln -sf "/var/www/html/public/$TARBALL_NAME" "/var/www/html/public/UNIT3D_Source_LATEST.tar.gz"
    ```

3. **Make the script executable:**

    ```sh
    chmod +x /var/www/html/create_tarball.sh
    ```

4. **Run the script manually whenever you update your site:**

    ```sh
    /var/www/html/create_tarball.sh
    ```

## 3. Creating and Updating the Open Source Page

1. **Create an Open Source page:**

    Go to your site's `/dashboard/pages` section and create a new page called "Open Source."

2. **Add the following Markdown content to the page:**

    ```markdown
    ## Open Source

    We comply with the UNIT3D's GNU Affero General Public License (AGPL) by sharing our modified source code. You can download the latest version of our source code below.

    - **[Download Latest Source Code](/UNIT3D_Source_LATEST.tar.gz)**

    ### License Information

    Our site runs on a modified version of [UNIT3D](https://github.com/HDInnovations/UNIT3D-Community-Edition). For more details on the license, visit the [GNU AGPL License](https://github.com/HDInnovations/UNIT3D-Community-Edition/blob/master/LICENSE).
    ```

3. **Manually update this page whenever you update your site:**

    After running the tarball creation script, update the page content if necessary to reflect any changes or additional notes about the modifications made.

## 4. Encouraging Compliance and Contributions

By publicly sharing your modified source code, you not only comply with the AGPL but also contribute to the open-source community. We encourage sites to contribute their changes back to the upstream repository by submitting pull requests, which helps improve UNIT3D for everyone.