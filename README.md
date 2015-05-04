#KDSM
2015 metų pavasario NFQ akademijos KDSM komandos projektas - stalo futbolo web appsas.

__Komandos nariai__: Simas Joneliūnas, Dainius Šležas, Martynas Stankevičius

__Komandos mentorius__: Kęstutis Bartkus

Fresh install after cloning from Git: (caution as fixtures:load command will truncate the database tables)

    connection to vagrant:
        open gitbash
        type: vagrant ssh

    php app/console doctrine:schema:update  --force

    php app/console doctrine:fixtures:load

Download latest data from api by console: (not using --env=prod leads to memory leak during processing
php app/console api:getlatest --all --env=prod


Crontab cronjob file contents:
* * * * * /usr/bin/php /var/www/app/console api:getlatest --all >> /var/www/app/logs/cronlog.txt
* * * * * sleep 10; /usr/bin/php /var/www/app/console api:getlatest --all >> /var/www/app/logs/cronlog.txt
* * * * * sleep 20; /usr/bin/php /var/www/app/console api:getlatest --all >> /var/www/app/logs/cronlog.txt
* * * * * sleep 30; /usr/bin/php /var/www/app/console api:getlatest --all >> /var/www/app/logs/cronlog.txt
* * * * * sleep 40; /usr/bin/php /var/www/app/console api:getlatest --all >> /var/www/app/logs/cronlog.txt
* * * * * sleep 50; /usr/bin/php /var/www/app/console api:getlatest --all >> /var/www/app/logs/cronlog.txt
* * * * * /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 6; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 12; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 18; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 24; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 30; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 36; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 42; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 48; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
* * * * * sleep 54; /usr/bin/php /var/www/app/console cont:calculate >> /var/www/app/logs/cronlog.txt
