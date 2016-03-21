FROM alpine

RUN apk add --update \
    nodejs \
    git \
    php-cli \
    php-phar \
    php-zlib \
    php-bz2 \
    php-ctype \
    php-xml \
    php-dom \
    curl \
    bash \
    && rm -rf /var/cache/apk/*


RUN mkdir -p /repo/tests

ENV HOME="/tmp"
USER nobody
WORKDIR /repo/tests

CMD npm install && node .
