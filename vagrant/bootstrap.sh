#!/bin/bash

apt-get update -qq

apt-get install -y --quiet git curl php5-cli php5-curl php5-sqlite

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
