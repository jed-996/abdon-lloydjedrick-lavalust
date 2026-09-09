FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev ca-certificates \
 && docker-php-ext-install pdo_mysql mbstring \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY . .
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN chmod +x docker/start.sh \
 && printf 'display_errors=Off\nlog_errors=On\nexpose_php=Off\n' > /usr/local/etc/php/conf.d/production.ini
EXPOSE 80
CMD ["sh", "docker/start.sh"]
