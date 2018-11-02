## Introduction

> Objective: Build a web app that allows to send tweet-sized text messages.

This web app is splitted into three loosely coupled parts -- repos that can run in different environments -- according to a microservice architecture.

| Repo              | Description                                                                                |
|-------------------|--------------------------------------------------------------------------------------------|
| `sms`             | JWT-authenticated API and RabbitMQ producer                                                |
| `sms-spa`         | React SPA created with [`create-react-app`](https://github.com/facebook/create-react-app)  |
| `sms-consumer`    | RabbitMQ consumer. PHP script using [`php-amqplib`](https://github.com/php-amqplib/php-amqplib)                                                      |

> **Note**: The RabbitMQ producer does not share its codebase with the consumer.

More specifically, the Symfony producer in `sms` is built with `php-amqplib/rabbitmq-bundle`. However, the consumer in `sms-consumer` is a PHP script written with `php-amqplib` -- for the sake of simplicity we are considering not to use a framework in that repo.


SMS
===

This is the `sms` repo, a JWT-authenticated API that plays the role of a RabbitMQ producer also.

### Start the Docker Services

    docker-compose up --build

### Install the Dependencies

    docker exec -it --user 1000:1000 sms_php_fpm composer install

The `www-data` group needs write permissions to the `var` folder:

    sudo chmod 775 -R var
    sudo chown -R $USER:www-data var

### Parameters Setup

Copy and paste the following into your `app/config/parameters.yml` file:

    parameters:
        database_driver: pdo_mysql
        database_host: 172.27.0.5
        database_port: 3306
        database_name: sms
        database_user: root
        database_password: password

        jwt_secret: example_secret_for_testing_only

        mailer_transport: smtp
        mailer_host: 127.0.0.1
        mailer_user: null
        mailer_password: null

        rabbitmq_host: 172.27.0.3
        rabbitmq_port: 5672
        rabbitmq_user: sms
        rabbitmq_password: password
        rabbitmq_vhost: /

        secret: a2cc952fcbfc869e47c220e8944d73d9ccd89cba

Please note, the value of `database_host` is replaced from `127.0.0.1` to `172.26.0.2`, which is the IP of the MySQL container. The same thing goes for `rabbitmq_host`.

The `IPAddress` is obtained this way:

    docker inspect sms_mysql
    docker inspect sms_rabbitmq

### Bootstrap the Testing Database

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
        "tel": "07412345678",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
    }'

    {
      "status": 401,
      "message": "Unauthorized"
    }

Example:

    curl -X POST -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MiwiZXhwIjoxNTQxMDIxNDMyfQ.niozdpQJW-WBsdSNfwkXsPraRbJR8tks4gZhKd9k8Fo' -i http://localhost:8080/api/message/send --data '{
        "tel": "foo",
        "content": ""
    }'

    {
        "status": 422,
        "message": ["The tel number is not valid", "The content cannot be blank"]
    }

Example:

    curl -X POST -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MiwiZXhwIjoxNTQxMDIxNDMyfQ.niozdpQJW-WBsdSNfwkXsPraRbJR8tks4gZhKd9k8Fo' -i http://localhost:8080/api/message/send --data '{
        "tel": "07412345678",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
    }'

    {
        "status": 200,
        "message": "Message successfully queued"
    }

### `/message/listing`

| Method       | Description                                |
|--------------|--------------------------------------------|
| `GET`        | Gets a listing of all SMS messages sent    |

Example:

    curl -X GET -i http://localhost:8080/api/message/listing

    {
        "status": 401,
        "message": "Unauthorized"
    }

Example:

    curl -X GET -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MiwiZXhwIjoxNTQxMDIxNDMyfQ.niozdpQJW-WBsdSNfwkXsPraRbJR8tks4gZhKd9k8Fo' -i http://localhost:8080/api/message/listing

    {
      "status": 200,
      "result": [{
        "user_id": 1,
        "tel": "07123456789",
        "content": "foo",
        "status": "queued",
        "publishedAt": "2018-11-01T22:47:13+00:00"
      }, {
        "user_id": 2,
        "tel": "07012345678",
        "content": "bar",
        "status": "queued",
        "publishedAt": "2018-11-01T22:47:13+00:00"
      }]
    }
