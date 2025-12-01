#!/bin/bash
set -e

echo "Waiting for MySQL to be ready..."
while ! nc -z mysql 3306; do
  sleep 1
done

echo "MySQL is ready!"

# Create .env if it doesn't exist
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cat > .env << EOF
APP_NAME=PaymentFacilitator
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:9000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=payment_facilitator
DB_USERNAME=laravel
DB_PASSWORD=laravel_password

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null
EOF
fi

# Install dependencies if vendor/autoload.php doesn't exist
if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts || {
        echo "Composer install failed, trying again..."
        composer install --no-interaction --prefer-dist
    }
fi

# Verify autoload.php exists
if [ ! -f "vendor/autoload.php" ]; then
    echo "ERROR: vendor/autoload.php still not found after composer install!"
    exit 1
fi

# Install npm dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "Installing NPM dependencies..."
    npm install || echo "NPM install failed, continuing..."
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --force || true
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || true

# Set permissions
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "Setup complete!"

exec "$@"

