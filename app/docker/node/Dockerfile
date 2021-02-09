FROM node:12-alpine

RUN apk --no-cache add pkgconfig autoconf automake libtool nasm build-base zlib-dev python libpng-dev git

WORKDIR /home/node/app

CMD /bin/sh -c yarn install && yarn watch