#!/bin/bash

mkdir -p /var/log/janat/laravel_storage
mkdir -p /var/log/janat/public/img/slider

mkdir -p /var/log/janat/nginx
mkdir -p /var/log/janat/php
mkdir -p /var/log/janat/supervisor

chown -R nobody:nogroup /var/log/janat
