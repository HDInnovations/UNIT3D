Welcome to the official documentation for **UNIT3D** - a modern, feature-rich private torrent tracker platform built with Laravel.

### About UNIT3D

UNIT3D (pronounced "united") is a powerful, open-source private torrent tracker solution that provides a comprehensive platform for managing and sharing content within your community. Built from the ground up with modern web technologies, UNIT3D combines excellent performance, security, and scalability with a feature-rich interface that rivals other private tracker softwares around the world. UNIT3D is a complete BitTorrent tracker management system designed for private communities. It handles everything from user management, torrent uploads and downloads, peer tracking, to community features like forums, achievements, real-time chat and much more. The codebase follows the MVC (Model-View-Controller) architectural pattern to ensure clarity between business logic and presentation, making it maintainable and extensible.

### Technology Stack

UNIT3D is built on a modern technology stack:

#### Backend
- **PHP 8.4+** - Latest PHP version with performance enhancements and type safety
- **Laravel 12** - The leading PHP framework for web artisans
- **Livewire 3** - Full-stack framework for building dynamic interfaces without leaving Laravel
- **Laravel Scout** - Full-text search with Meilisearch integration
- **MySQL 8+** - Relational database with strict mode compliance
- **Redis** - In-memory data structure store for caching and queues

#### Frontend
- **AlpineJS 3** - Lightweight JavaScript framework for reactive interfaces
- **Vite** - Next-generation frontend build tool
- **Livewire 3** - Reactive components without writing JavaScript
- **Socket.io** - Real-time bidirectional event-based communication

#### Development & Quality
- **Pest** - Elegant testing framework
- **Larastan** - PHPStan wrapper for Laravel static analysis
- **Pint** - Opinionated PHP code style fixer
- **Prettier + Blade Plugin** - Blade code formatting
- **ParaTest** - Parallel testing for faster test execution
- **Laravel Debugbar** - Debugging and profiling

## Project Structure

UNIT3D follows Laravel's conventional directory structure with some custom additions:

```
UNIT3D/
├── app/                          # Application core
│   ├── Achievements/             # Achievement system
│   ├── Actions/                  # Single-purpose action classes (Fortify)
│   ├── Bots/                     # IRC and other bot integrations
│   ├── Console/                  # Artisan commands
│   ├── DTO/                      # Data Transfer Objects
│   ├── Enums/                    # Enumerations
│   ├── Events/                   # Event classes
│   ├── Exceptions/               # Custom exception handlers
│   ├── Helpers/                  # Helper functions
│   ├── Http/                     # Controllers, middleware, requests, livewire components, resources
│   ├── Interfaces/               # Contracts and interfaces
│   ├── Jobs/                     # Queued jobs
│   ├── Listeners/                # Event listeners
│   ├── Mail/                     # Mail classes
│   ├── Models/                   # Eloquent models
│   ├── Notifications/            # Notification classes
│   ├── Observers/                # Model observers
│   ├── Providers/                # Service providers
│   ├── Repositories/             # Data access layer
│   ├── Rules/                    # Validation rules
│   ├── Services/                 # External services (Rust announce, TMDB, IGDB)
│   ├── Traits/                   # Reusable traits
│   └── View/                     # View composers
│
├── bootstrap/                    # Framework bootstrap
│   ├── app.php                   # Application bootstrap
│   └── cache/                    # Compiled services and packages
│
├── config/                       # Configuration files
│   ├── app.php                   # Core application config
│   ├── database.php              # Database connections
│   ├── torrent.php               # Torrent-specific settings
│   ├── unit3d.php                # UNIT3D custom config
│   └── ...                       # 40+ configuration files
│
├── database/                     # Database layer
│   ├── factories/                # Model factories for testing
│   ├── migrations/               # Database migrations
│   ├── schema/                   # Database schema dumps
│   └── seeders/                  # Database seeders
│
├── lang/                         # Internationalization (60+ languages)
│   ├── en/                       # English translations
│   ├── es/                       # Spanish translations
│   ├── fr/                       # French translations
│   └── ...                       # 60+ language directories
│
├── public/                       # Web server document root
│   ├── index.php                 # Entry point
│   ├── build/                    # Compiled assets (Vite)
│   ├── img/                      # Images
│   └── sounds/                   # Audio files
│
├── resources/                    # Raw assets and views
│   ├── js/                       # JavaScript source files
│   ├── sass/                     # SCSS/Sass stylesheets
│   └── views/                    # Blade templates
│
├── routes/                       # Application routes
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   ├── announce.php              # Torrent announce routes
│   ├── rss.php                   # RSS feed routes
│   ├── chat.php                  # Chat component routes
│   └── channels.php              # WebSocket channels
│
├── storage/                      # Generated files and logs
│   ├── app/                      # User uploads
│   ├── framework/                # Framework generated files
│   ├── logs/                     # Application logs
│   └── backups/                  # Automated backups
│
├── tests/                        # Automated tests
│   ├── Feature/                  # Feature tests
│   ├── Unit/                     # Unit tests
│   └── Pest.php                  # Pest configuration
│
├── book/                         # Documentation (mdBook)
│   └── src/                      # Documentation source
│
├── artisan                       # Laravel CLI tool
├── composer.json                 # PHP dependencies
├── package.json                  # Node dependencies
├── vite.config.js                # Vite build configuration
├── phpstan.neon                  # Static analysis configuration
├── pint.json                     # Code style configuration
└── phpunit.xml                   # PHPUnit test configuration
```