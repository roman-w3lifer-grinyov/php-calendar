FROM php:8.0.30-cli

RUN apt-get update && apt-get install -y git unzip

# install-php-extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions zip
# /install-php-extensions

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --quiet

RUN groupadd --gid 1000 dockerGroup
RUN useradd --uid 1000 --gid dockerGroup --shell /bin/bash --create-home dockerUser

USER dockerUser
