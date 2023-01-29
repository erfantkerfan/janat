#!/bin/bash

docker service rm janat_production

docker service create \
    --name janat_production \
    --replicas=1 \
    --publish published=81,target=80,mode=ingress \
    --with-registry-auth \
    --init \
    --host host.machine:host-gateway \
    --mount type=bind,source=/var/log/janat/.env,destination=/var/www/html/.env \
    --mount type=bind,source=/var/log/janat/nginx,destination=/var/log/nginx \
    --mount type=bind,source=/var/log/janat/php,destination=/var/log/php \
    --mount type=bind,source=/var/log/janat/supervisor,destination=/var/log/supervisor \
    --mount type=bind,source=/var/log/janat/laravel_storage,destination=/var/www/html/storage \
    --mount type=bind,source=/var/log/janat/public/img/slider,destination=/var/www/html/public/img/slider \
    --mount type=tmpfs,destination=/var/nginx/cache,tmpfs-size=10m \
    -u nobody \
    erfantkerfan/janat:$1
