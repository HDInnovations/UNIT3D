<div align="center">
    <img width="400" alt="unit3d" src="https://github.com/user-attachments/assets/2fa3678d-015c-4438-bcdb-ac6508915551" />
</div>

<p align="center">
    <a href="http://laravel.com"><img src="https://img.shields.io/badge/Laravel-12-f4645f.svg" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/blob/master/LICENSE"><img src="https://img.shields.io/badge/License-AGPL%20v3.0-yellow.svg" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/lint.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/lint.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/phpunit-test.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/phpunit-test.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/compile-assets-test.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/compile-assets-test.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/larastan.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/larastan.yml/badge.svg?branch=master" /></a>
    <a href="https://github.com/HDInnovations/UNIT3D/actions/workflows/prettier-blade.yml/badge.svg?branch=master"><img src="https://github.com/HDInnovations/UNIT3D/actions/workflows/prettier-blade.yml/badge.svg?branch=master" /></a>
    <a href="https://hosted.weblate.org/engage/unit3d/">
    <img src="https://hosted.weblate.org/widget/unit3d/svg-badge.svg" alt="Translation status" />
    </a>
</p>

## 📝 Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Updating](#updating)
4. [Documentation](#docs)
5. [Products, Services and Support](#hdinnovations)
6. [Contributing](#contributing)
7. [Translations](#translations)
8. [License](#license)


## <a name="introduction"></a> 🧐 Introduction

UNIT3D (pronounced "united") is a modern Private Torrent Tracker software built with Laravel, Livewire and AlpineJS. It offers a feature-rich platform with excellent performance, security and scalability to create and manage a private tracker. It is MySQL Strict Mode Compliant and PHP 8.4 Ready. It uses an MVC Architecture to ensure clarity between logic and presentation.

## <a name="installation"></a> 🖥️ Installation

The official script is no longer available at this time. A new one will be provided soon.

## <a name="updating"></a> 🖥️ Updating

To update your installation to the latest version, run the following command. This will pull the latest changes from the repository and update your instance:

`sudo php artisan git:update`

## <a name="docs"></a> 📚 Documentation

https://hdinnovations.github.io/UNIT3D

## <a name="hdinnovations"></a> 🛠️ Products, Services and Support

HDInnovations offers a variety of services to help you with your UNIT3D instance. We offer services such as installation, updating, server tuning, dependency tuning, themes, porting from different codebase and more. We have a Discord server for support and general discussion. This is a private server, and you will need to be invited to join. There is a small fee to join the server to help support the development of UNIT3D.

https://hdinnovations.github.io/HDInnovations

## <a name="contributing"></a> 🤝 Contributing

Please read [CONTRIBUTING.md](https://github.com/HDInnovations/UNIT3D/blob/master/CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests to us. A massive thank you to all of our <a href="https://github.com/HDInnovations/UNIT3D/graphs/contributors">contributors</a>.

## <a name="translations"></a> 🌎 Translations

Vltava defaults to Czech (`cs`) with English (`en`) as the fallback for missing translations in other locales. An explicit `?lang=` override takes precedence over the account language, then the guest session language; request overrides do not change saved preferences. Invalid or non-string locale values fall back to the configured default.

Keep action labels separate from transfer totals and completed states: `common.upload-action` / `common.download-action`, `common.uploaded` / `common.downloaded`. Translate complete sentences with named placeholders, not English grammar fragments. Use the existing `interface`, `member-interface`, `media-interface`, `livewire-interface`, `staff-interface`, and `application-messages` catalogues for first-party text. JavaScript receives escaped JSON from Blade; generated CSS labels use localized `data-label` attributes.

Notifications and queued mail use the recipient's account language. Shared chat and IRC broadcasts use the site's default language. User-authored content, metadata titles, protocol values, and identifiers are not translated. Achievement descriptions must be rendered with `App\Achievements\Achievement::descriptionFor()`; do not instantiate achievements for presentation, because the vendor constructor synchronizes shared database metadata.

Run `docker compose exec -T laravel.test php scripts/audit-i18n.php` to check literal translation references (including achievement description constants), Czech catalogue completeness, duplicate English/Czech keys, and matching named placeholders. It exits nonzero on defects and does not boot Laravel or access the database. This structural check does not replace reviewing translation meaning or exercising dynamic translation paths.

We use Weblate for translations. You can easily contribute to translations at https://hosted.weblate.org/engage/unit3d/. Use the following graphic to see if your native language could use some work.

<a href="https://hosted.weblate.org/engage/unit3d/">
<img src="https://hosted.weblate.org/widget/unit3d/horizontal-auto.svg" alt="Translation status" />
</a>

## <a name="license"></a> 📜 License

This project is licensed under the AGPL v3.0 License. See the [LICENSE](https://github.com/HDInnovations/UNIT3D/blob/master/LICENSE.md) file for details.


