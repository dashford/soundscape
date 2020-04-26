FROM php:7.4.4-fpm-alpine

ENV COMPOSER_VERSION=1.10.5

ENV COMPOSER_HOME /root/composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "copy('https://composer.github.io/installer.sig', 'installer.sig');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === file_get_contents('installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION} \
    && php -r "unlink('composer-setup.php');"