FROM php:8.3-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev zip libzip-dev unzip nginx bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy codebase
COPY . .

# Setup permissions for Laravel logging and caching
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy custom Nginx configuration (defined in Step 3)
COPY ./nginx.conf /etc/nginx/nginx.conf

# Expose Render's default port
EXPOSE 10000

# Make the deploy script executable and execute the server entrypoint
RUN chmod +x ./render-deploy.sh
CMD ["sh", "-c", "./render-deploy.sh && php-fpm -D && nginx -g 'daemon off;'"]
