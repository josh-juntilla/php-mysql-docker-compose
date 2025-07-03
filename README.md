# PHP MySQL Development Environment

A complete Docker Compose setup for PHP and MySQL development with REST API support.

## Features

- PHP 8.2 with Apache
- MySQL 8.0 with persistent data storage
- PDO MySQL support
- Composer pre-installed
- REST API ready with proper routing
- CORS headers configured
- Development-friendly PHP configuration

## Quick Start

1. **Create the project structure:**
```bash
mkdir php-mysql-dev && cd php-mysql-dev
```

2. **Create the required files:**
   - Copy all the provided files to your project directory
   - Create the following directory structure:
```
php-mysql-dev/
├── docker-compose.yml
├── Dockerfile
├── php.ini
├── src/
│   ├── index.php
│   ├── .htaccess
│   └── api/
│       └── index.php
└── mysql/
    └── init/
        └── (optional SQL files for initialization)
```

3. **Start the services:**
```bash
docker-compose up -d
```

4. **Access your application:**
   - Web interface: http://localhost:8080
   - MySQL: localhost:3306
   - API endpoints: http://localhost:8080/api/

## Directory Structure

- `src/` - Your PHP application code goes here
- `mysql/init/` - SQL files placed here will run on first MySQL startup
- MySQL data is persisted in a Docker volume

## Database Configuration

**Connection Details:**
- Host: `mysql` (from within PHP container) or `localhost` (from host machine)
- Port: `3306`
- Database: `dev_db`
- Username: `dev_user`
- Password: `dev_password`
- Root password: `root_password`

**Environment Variables Available in PHP:**
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_NAME=dev_db`
- `DB_USER=dev_user`
- `DB_PASSWORD=dev_password`

## Using Composer

Run Composer commands inside the PHP container:

```bash
# Install dependencies
docker-compose exec php composer install

# Add a new package
docker-compose exec php composer require package-name

# Update dependencies
docker-compose exec php composer update
```

## API Development

The setup includes a basic REST API structure:

**Available Endpoints:**
- `GET /api/health` - Health check
- `GET /api/users` - List users
- `POST /api/users` - Create user

**Example API Request:**
```bash
# Create a user
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com"}'

# Get all users
curl http://localhost:8080/api/users
```

## Development Tips

1. **Live Code Changes:** Place your PHP files in the `src/` directory - changes are reflected immediately
2. **Database Access:** Use the provided PDO connection example in `index.php`
3. **Custom PHP Configuration:** Modify `php.ini` and restart the container
4. **MySQL Initialization:** Place `.sql` files in `mysql/init/` for database setup

## Common Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Access PHP container shell
docker-compose exec php bash

# Access MySQL shell
docker-compose exec mysql mysql -u dev_user -p dev_db

# Restart a specific service
docker-compose restart php
```

## Customization

**Change PHP Version:**
Edit the `FROM` line in `Dockerfile`:
```dockerfile
FROM php:8.1-apache  # or php:7.4-apache
```

**Add PHP Extensions:**
Add to the `docker-php-ext-install` line in `Dockerfile`:
```dockerfile
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd zip xml curl
```

**Change MySQL Version:**
Edit the MySQL service in `docker-compose.yml`:
```yaml
mysql:
  image: mysql:5.7  # or mariadb:10.6
```

## Troubleshooting

**Permission Issues:**
```bash
sudo chown -R $USER:$USER src/
```

**MySQL Connection Refused:**
Wait a few seconds after starting for MySQL to fully initialize, or check logs:
```bash
docker-compose logs mysql
```

**Port Already in Use:**
Change the ports in `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Instead of 8080:80
```