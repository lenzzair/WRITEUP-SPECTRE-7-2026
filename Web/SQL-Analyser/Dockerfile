FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    mariadb-server \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

COPY app/html/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html

COPY db-init/init.sql /init.sql
COPY start.sh /start.sh
RUN chmod +x /start.sh

RUN echo "mysqli.default_socket=" >> /usr/local/etc/php/conf.d/docker-php-mysqli.ini
RUN echo "pdo_mysql.default_socket=" >> /usr/local/etc/php/conf.d/docker-php-mysql.ini


EXPOSE 80
CMD ["/start.sh"]

