FROM ezsystems/php:8.1

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

ENV COMPOSER_HOME=~/.composer
RUN composer self-update
RUN chown 1000:1000 /usr/local/bin/composer


RUN mv /usr/local/etc/php/conf.d/php.ini /usr/local/etc/php/php.ini \
    && echo 'date.timezone = Europe/Paris' >> /usr/local/etc/php/php.ini \
    && cp /usr/local/etc/php/php.ini /usr/local/etc/php/php-cli.ini \
    && echo 'memory_limit = 512M' >> /usr/local/etc/php/php.ini \
    && echo 'memory_limit = -1' >> /usr/local/etc/php/php-cli.ini \
    && echo 'max_execution_time = -1' >> /usr/local/etc/php/php-cli.ini \

# Blackfire layers
#RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
#    && architecture=$(uname -m) \
#    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/$architecture/$version \
#    && mkdir -p /tmp/blackfire \
#    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
#    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
#    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8307\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
#    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz
#
#
#RUN mkdir -p /tmp/blackfire \
#    && architecture=$(uname -m) \
#    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/cli/linux/$architecture | tar zxp -C /tmp/blackfire \
#    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
#    && rm -Rf /tmp/blackfire

