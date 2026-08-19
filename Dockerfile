FROM php:8.3-apache

RUN a2enmod rewrite headers

WORKDIR /var/www/html

COPY . /var/www/html

RUN chmod +x /var/www/html/render-start.sh \
    && chown -R www-data:www-data /var/www/html/runtime \
    && chmod -R 775 /var/www/html/runtime

ENV PORT=10000

EXPOSE 10000

CMD ["/var/www/html/render-start.sh"]
