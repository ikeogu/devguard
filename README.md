# DevGuard - Laravel Development Monitoring Package

A comprehensive Laravel package that provides development monitoring and guard functionality with integrated tools for logging, API documentation, and application monitoring giving access to only authorized users.

## Features

- **Development User Management** - Secure authentication system for development environments
- **Integrated Log Viewer** - Built-in log viewing capabilities via [Log Viewer](https://github.com/opcodesio/log-viewer)
- **API Documentation** - Automatic API documentation generation with [Scramble](https://github.com/dedoc/scramble)
- **Application Monitoring** - Advanced debugging and monitoring with [Laravel Telescope](https://github.com/laravel/telescope)
- **Custom Authentication Guard** - Specialized auth guard for development users
- **React/JS Integration** - Frontend components and build configuration
- **Database Seeding** - Pre-configured development user seeding

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or 11.0, 12.0
- Composer

## Installation

### 1. Install the Package

```bash
composer require zojatech/devguard 
or
composer require zojatech/devguard:dev-main 
```

This will automatically install all required dependencies:
- `opcodesio/log-viewer`
- `dedoc/scramble` 
- `laravel/telescope`

### 2. Publish Package Assets

Publish all package files and configurations:

```bash
php artisan vendor:publish --tag=dev-guard-all
```

This will publish:
- Views to `resources/views/vendor/dev-guard`
- JavaScript/React components to `resources/js/vendor/dev-guard`
- Database migration for dev users table
- Database seeder for default dev user
- Configuration files for all integrated packages

### 3. Run Database Migration

```bash
php artisan migrate
```

### 4. Seed Default Development User

```bash
php artisan db:seed --class=DevUserSeeder
```

This creates a default development user:
- **Email:** `dev@local.test`
- **Password:** `password`

## Configuration

### DevGuard Configuration

The main configuration file is published to `config/devguard.php`. Customize it according to your needs:

```php

return [
    'enabled' => true,
    
    // Add your package-specific configurations here
];
```

### Integrated Tools Configuration

The package automatically publishes configuration files for all integrated tools:

- **Log Viewer:** `config/log-viewer.php`
- **Scramble API Docs:** `config/scramble.php` 
- **Telescope:** `config/telescope.php`

Refer to each tool's documentation for specific configuration options.

## Usage

### Development Authentication

The package provides a custom authentication guard for development users. Use the `DevUser` model for development-specific authentication:

```php
use ZojaTech\DevGuard\Models\DevUser;

// Example usage in your controllers
$devUser = DevUser::where('email', 'dev@local.test')->first();
```

### Accessing Integrated Tools

Once installed and configured:

- **Log Viewer:** Visit `/log-viewer` (configure route in log-viewer config)
- **API Documentation:** Visit `/docs/api` (configure route in scramble config)
- **Telescope:** Visit `/telescope` (configure route in telescope config)

## Publishing Options

You can publish specific parts of the package separately:

```bash

# Force republish all files (overwrites existing)
php artisan vendor:publish --tag=dev-guard-all --force
```

### Service Provider

The package automatically registers all necessary service providers and handles:

- Custom authentication guard registration
- Route loading
- View loading
- Configuration merging
- Conditional vendor package registration

## Troubleshooting

### Common Issues

**Migration already exists:**
- Use `--force` flag when republishing: `php artisan vendor:publish --tag=dev-guard-all --force`

**Vendor configs not found:**
- Ensure all dependencies are installed: `composer install`
- Check if specific packages are available in your `vendor` directory

**Permission issues:**
- Ensure your web server has write permissions to storage and bootstrap/cache directories

### Re-installation

To completely reinstall the package:

```bash
# Remove published files
rm config/devguard.php config/log-viewer.php config/scramble.php config/telescope.php
rm database/seeders/DevUserSeeder.php
rm -rf resources/views/vendor/dev-guard resources/js/vendor/dev-guard

# Republish
php artisan vendor:publish --tag=dev-guard-all
php artisan migrate:fresh
php artisan db:seed --class=DevUserSeeder
```

### Uninstall Package

```bash
php artisan devguard:cleanup --force 
composer remove zojatech/devguard


## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

If you encounter any issues or have questions:

1. Check the [troubleshooting section](#troubleshooting)
2. Review the documentation for integrated tools:
   - [Log Viewer Documentation](https://github.com/opcodesio/log-viewer)
   - [Scramble Documentation](https://github.com/dedoc/scramble)
   - [Telescope Documentation](https://laravel.com/docs/telescope)
3. Open an issue on the GitHub repository

## Credits

This package integrates and builds upon these excellent packages:
- [Log Viewer](https://github.com/opcodesio/log-viewer) by Opcodesio
- [Scramble](https://github.com/dedoc/scramble) by Dedoc
- [Laravel Telescope](https://github.com/laravel/telescope) by Laravel