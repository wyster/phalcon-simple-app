#!/bin/bash
echo 'Run start.sh'

composer global require hirak/prestissimo
composer install

# Enable xdebug
XdebugFile='/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini'
if [[ "$ENABLE_XDEBUG" == "1" ]] ; then
  if [ -f $XdebugFile ]; then
    echo "Xdebug enabled"
  else
    echo "Enabling xdebug"
    echo "If you get this error, you can safely ignore it: /usr/local/bin/docker-php-ext-enable: line 83: nm: not found"
    # see https://github.com/docker-library/php/pull/420
    docker-php-ext-enable xdebug
    # see if file exists
    if [ -f $XdebugFile ]; then
      # See if file contains xdebug text.
      if grep -q xdebug.remote_enable "$XdebugFile"; then
        echo "Xdebug already enabled... skipping"
      else
        echo "xdebug.remote_enable=1"  >> $XdebugFile
        echo "xdebug.remote_connect_back=1" >> $XdebugFile
      fi
    fi
  fi
else
  if [ -f $XdebugFile ]; then
    echo "Disabling Xdebug"
    rm $XdebugFile
  fi
fi

if [ ! -z "$USE_PHP_INTERNAL_SERVER" ]; then
  php -d xdebug.remote_enable=1 -d xdebug.remote_connect_back=1 -S 0.0.0.0:80 -t public .htrouter.php
else
  docker-php-entrypoint php-fpm
fi
