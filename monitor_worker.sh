#!/bin/bash

# Vérifier si le worker queue:listen est actif
if ! pgrep -f "php artisan queue:listen" > /dev/null; then
    # Si le worker n'est pas actif, le redémarrer
    nohup php /home/u474923210/public_html/biicf/artisan queue:listen > /home/u474923210/public_html/biicf/storage/logs/queue.log 2>&1 & 
    disown
fi

