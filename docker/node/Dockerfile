FROM node:24-alpine

RUN apk add --no-cache php84 php84-cli php84-mbstring php84-openssl php84-tokenizer php84-xml php84-phar php84-curl php84-ctype

RUN ln -sf /usr/bin/php84 /usr/bin/php

WORKDIR /var/www/html
