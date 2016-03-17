FROM alpine

RUN apk add --update \
    nodejs \
    git \
    php-cli \
    && rm -rf /var/cache/apk/*

# install gulp
RUN npm install -g gulp
RUN mkdir -p /repo/tests

ENV HOME="/tmp"
USER nobody
WORKDIR /repo/tests

CMD npm install && gulp 
