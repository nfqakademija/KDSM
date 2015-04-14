#KDSM
2015 metų pavasario NFQ akademijos KDSM komandos projektas - stalo futbolo web appsas.

__Komandos nariai__: Simas Joneliūnas, Dainius Šležas, Martynas Stankevičius

__Komandos mentorius__: Kęstutis Bartkus

Fresh install after cloning from Git: (caution as fixtures:load command will truncate the database tables)
    php app/console doctrine:schema:update  --force
    php app/console doctrine:fixtures:load
