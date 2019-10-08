#!/bin/sh
set -e

INSTALL_VERSION=3.4.2

if [ "$1" != "" ]; then
    INSTALL_VERSION=$1
fi

if [ ! -d /usr/src/phalcon-devtools ]; then
  echo 'download phalcon-devtools'
  curl -LO https://github.com/phalcon/phalcon-devtools/archive/v${INSTALL_VERSION}.tar.gz
  tar xzf v${INSTALL_VERSION}.tar.gz
  rm -rf v${INSTALL_VERSION}.tar.gz
  mv phalcon-devtools-${INSTALL_VERSION} /usr/src/phalcon-devtools
  ln -sf /usr/src/phalcon-devtools/phalcon /usr/local/bin/phalcon
fi
