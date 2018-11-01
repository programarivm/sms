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


## API Endpoints

### `/auth`

| Method       | Description                                |
|--------------|--------------------------------------------|
| `POST`        | Gets a new access token                    |

Example:

    curl -X POST -i http://localhost:8080/api/auth --data '{
        "username": "bob",
        "password": "password"
    }'

    {
        "status": 200,
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MiwiZXhwIjoxNTQxMDIxNDMyfQ.niozdpQJW-WBsdSNfwkXsPraRbJR8tks4gZhKd9k8Fo"
    }

### `/message/send`

| Method       | Description                                |
|--------------|--------------------------------------------|
| `POST`       | Sends a new message                        |

Example:

    curl -X POST -i http://localhost:8080/api/message/send --data '{
        "telephone": "07412345678",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
    }'

    {
      "status": 401,
      "message": "Unauthorized"
    }

Example:

    curl -X POST -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MiwiZXhwIjoxNTQxMDIxNDMyfQ.niozdpQJW-WBsdSNfwkXsPraRbJR8tks4gZhKd9k8Fo' -i http://localhost:8080/api/message/send --data '{
        "telephone": "foo",
        "content": ""
    }'

    {
        "status": 422,
        "message": ["The telephone number is not valid", "The content cannot be blank"]
    }

Example:

    curl -X POST -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MiwiZXhwIjoxNTQxMDIxNDMyfQ.niozdpQJW-WBsdSNfwkXsPraRbJR8tks4gZhKd9k8Fo' -i http://localhost:8080/api/message/send --data '{
        "telephone": "07412345678",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
    }'

    {
        "status": 200,
        "message": "Message successfully queued"
    }
