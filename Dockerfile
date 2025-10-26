# Use official PHP Apache image
FROM php:8.2-apache

# Enable required Apache modules
RUN a2enmod rewrite headers

# Copy project files to web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set permissions (optional, helps on some hosts)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
