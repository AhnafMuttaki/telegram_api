# telegram_api
Telegram Bot API using getUpdates method

This is an API developed with RAW php using the telegram bot api's getUpdates method. It uses mysql as a caching server.Please follow the step by step procedure given below to use this api.


Step 1. Execute mysql command in database.sql to create database.


Step 2. Enter bot key,host_name,user_name,password in the config portion of telegram.php.


Step 3. Set your own logic of reply in prepare_reply() function of telegram.php.


Step 4. To get your Bot to contineu replying run telegram.php under a scheduler.



Created BY: Ahnaf Muttaki (July 19 2018) 


