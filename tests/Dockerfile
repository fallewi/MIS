FROM alpine

RUN apk add --update \
    nodejs \
    git \
    php5 \
    php5-phar \
    php5-zlib \
    php5-bz2 \
    php5-ctype \
    php5-xml \
    php5-dom \
    curl \
    bash \
    libxml2-utils \
    && rm -rf /var/cache/apk/*


RUN mkdir -p /repo/tests

ENV HOME="/tmp"
USER nobody
WORKDIR /repo/tests

CMD npm install && node .
