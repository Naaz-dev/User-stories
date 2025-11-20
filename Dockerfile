FROM php:8.2-apache

# Installeer benodigde PHP-extensies voor PDO / MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Zorg dat Apache prettige standaardinstellingen gebruikt
RUN a2enmod rewrite

# Kopieer onze applicatie
COPY src/ /var/www/

# Zorg dat Apache bestanden kan lezen/schrijven
RUN chown -R www-data:www-data /var/www

# Gebruik production settings (display_errors staat uit)
ENV APP_DEBUG=false

EXPOSE 80
