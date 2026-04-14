FROM php:7.4-apache

LABEL description="CTF Spectre-7 — LFI Challenge (Blue Team)"

RUN apt-get update && apt-get install -y curl nano vim openssh-server coreutils sudo && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers
RUN mkdir /var/run/sshd

RUN useradd -m -s /bin/bash admin \
    && echo "admin:spectre" | chpasswd

RUN usermod -aG sudo admin

RUN usermod -aG www-data admin \
    && chmod g+w /var/www/html

COPY config/apache.conf /etc/apache2/sites-available/000-default.conf
COPY www/ /var/www/html/
COPY logs/access_prebuilt.log /var/log/apache2/access_delta.log
COPY checker.sh /usr/local/bin/checker.sh
COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /usr/local/bin/checker.sh /entrypoint.sh
RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/log/apache2

RUN chmod 644 /var/log/apache2/access_delta.log


EXPOSE 80
EXPOSE 22
ENTRYPOINT ["/entrypoint.sh"]
