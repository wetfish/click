FROM docker.io/php:8.5.12-fpm-alpine

RUN docker-php-ext-install mysqli

# php's production defaults, plus click's own settings
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./config/php.ini "$PHP_INI_DIR/conf.d/click.ini"
COPY ./config/php-fpm-pool.conf /usr/local/etc/php-fpm.d/click.conf

# root owns the code, so php can read it but never change it
COPY ./public_html /var/www

RUN set -exu \
  && addgroup -g 1101 fishy \
  && adduser \
      -u 1101 \
      -G fishy \
      -H \
      -s /sbin/nologin \
      -D \
      fishy

USER fishy

# Expose port 9000 and start php-fpm server
EXPOSE 9000

WORKDIR /var/www

CMD ["php-fpm"]
