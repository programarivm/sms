SMS
===

> TODO. Write the documentation explaining how to run the app.

...

### Start the Docker Services

    docker-compose up --build

### Install the Dependencies

    docker exec -it --user 1000:1000 sms_php_fpm composer install

The `www-data` group needs write permissions to the `var` folder:

    sudo chmod 775 -R var
    sudo chown -R $USER:www-data var

### Bootstrap the Testing Database

Copy and paste the following into your `app/config/parameters.yml` file:

    parameters:
        database_driver: pdo_mysql
        database_host: 172.27.0.3
        database_port: 3306
        database_name: sms
        database_user: root
        database_password: password
        mailer_transport: smtp
        mailer_host: 127.0.0.1
        mailer_user: null
        mailer_password: null
        secret: a2cc952fcbfc869e47c220e8944d73d9ccd89cba

Please note, the value of `database_host` is replaced from `127.0.0.1` to `172.26.0.2`, which is the IP of the MySQL container.

The `IPAddress` is obtained this way:

    docker inspect sms

Then run:

    docker exec -it --user 1000:1000 sms_php_fpm php bin/console database:bootstrap


### Run the Tests

	docker exec -it --user 1000:1000 sms_php_fpm php vendor/bin/phpunit
