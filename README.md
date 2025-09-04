<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Translation API

A comprehensive Laravel-based API for managing multi-language translations with authentication support.

## 🚀 Quick Start

### Prerequisites

- **PHP**: 8.1 or higher
- **Composer**: Latest version
- **Database**: MySQL 8.0+ or PostgreSQL 13+ or SQLite 3
- **Node.js**: 18+ (for frontend assets, if needed)

### Option 1: Run with Docker (Recommended)

#### Using Docker Compose

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/translation-api.git
   cd translation-api
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```

4. **Install dependencies and setup**
   ```bash
   # Install PHP dependencies
   docker-compose exec app composer install
   
   # Generate application key
   docker-compose exec app php artisan key:generate
   
   # Run database migrations
   docker-compose exec app php artisan migrate
   
   # Seed database (optional)
   docker-compose exec app php artisan db:seed
   ```

5. **Access the application**
   - API: http://localhost:8000/api
   - API Documentation: http://localhost:8000/api-docs.html

#### Docker Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f app

# Access container shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan test
```

### Option 2: Run without Docker

#### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/translation-api.git
   cd translation-api
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=translation_api
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE translation_api;"
   
   # Run migrations
   php artisan migrate
   
   # Seed database (optional)
   php artisan db:seed
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

7. **Access the application**
   - API: http://localhost:8000/api
   - API Documentation: http://localhost:8000/api-docs.html

#### Alternative: Using Laravel Sail (Laravel's built-in Docker solution)

1. **Install Laravel Sail**
   ```bash
   composer require laravel/sail --dev
   ```

2. **Publish Sail configuration**
   ```bash
   php artisan sail:install
   ```

3. **Start Sail**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Run commands with Sail**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan test
   ```

## 🧪 Testing

### Run all tests
```bash
# With Docker
docker-compose exec app php artisan test

# Without Docker
php artisan test

# With Sail
./vendor/bin/sail artisan test
```

### Run specific test suites
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# Performance tests only
php artisan test --testsuite=Performance
```

### Run tests with coverage
```bash
php artisan test --coverage
```

## 📚 API Documentation

- **Swagger/OpenAPI**: `swagger.yaml` - Import to SwaggerHub
- **Local HTML**: `api-docs.html` - View in browser
- **README**: `README-API-Documentation.md` - Detailed usage instructions

## 🔧 Configuration

### Environment Variables

Key environment variables in `.env`:

```env
APP_NAME="Translation API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=translation_api
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Database Configuration

The application supports multiple database drivers:

- **MySQL**: Production-ready, full JSON support
- **PostgreSQL**: Production-ready, excellent JSON support
- **SQLite**: Development/testing, limited JSON support

## 🌟 Features

- **Authentication**: Laravel Sanctum JWT tokens
- **Translations**: Multi-language support with context grouping
- **Search**: Full-text search across translations
- **Validation**: Comprehensive request validation
- **Testing**: Unit, feature, and performance tests
- **Documentation**: OpenAPI 3.0 specification
- **Performance**: Optimized database queries and caching

## 🔐 Authentication

The API uses Bearer token authentication:

1. **Login** to get a token: `POST /api/login`
2. **Include token** in requests: `Authorization: Bearer {token}`

## 📖 API Endpoints

- `POST /api/login` - User authentication
- `GET /api/translation/get/{context}/{locale}` - Get translations
- `POST /api/translation/create` - Create translation
- `PATCH /api/translation/update` - Update translation
- `GET /api/translation/search/{keyword}` - Search translations

## 🚨 Troubleshooting

### Common Issues

1. **Permission denied errors**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

2. **Database connection issues**
   - Check database credentials in `.env`
   - Ensure database service is running
   - Verify database exists

3. **Composer autoload issues**
   ```bash
   composer dump-autoload
   ```

4. **Cache issues**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```

### Getting Help

- Check the [Laravel documentation](https://laravel.com/docs)
- Review the [API documentation](README-API-Documentation.md)
- Open an issue on GitHub

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
