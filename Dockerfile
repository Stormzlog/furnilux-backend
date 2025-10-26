# Use official PHP image with Apache
FROM php:8.2-apache

# Enable common PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files
COPY . /var/www/html/

# Allow cross-origin requests (CORS)
RUN echo 'Header set Access-Control-Allow-Origin "*"' >> /etc/apache2/apache2.conf && \
    echo 'Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"' >> /etc/apache2/apache2.conf && \
    echo 'Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"' >> /etc/apache2/apache2.conf

# Expose web server port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
