#!/bin/bash

ps ax | grep '/usr/local/php72/bin/php -c /home/avtomale/.system/php/www.farbid.com.ua.ini -f /home/avtomale/farbid.com.ua/www/artisan queue:listen --tries=3 --sleep=3 --timeout=30' > tmp

if [ `cat tmp | wc -l` -ne "2" ]
then
#killall -9 php
/usr/local/php72/bin/php -c /home/avtomale/.system/php/www.farbid.com.ua.ini -f /home/avtomale/farbid.com.ua/www/artisan queue:retry all
/usr/local/php72/bin/php -c /home/avtomale/.system/php/www.farbid.com.ua.ini -f /home/avtomale/farbid.com.ua/www/artisan queue:listen --tries=3 --sleep=3 --timeout=30 &
fi
rm tmp
