<?php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class TokenAuthenticatedWebTestCase extends WebTestCase
{
    protected static $accessToken;

    public static function setUpBeforeClass()
    {
        $client = static::createClient();

        $user = [
            'username' => 'bob',
            'password' => 'password',
        ];

        $client->request(
            'POST',
            '/api/auth',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($user)
        );

        $content = json_decode($client->getResponse()->getContent());

        self::$accessToken = $content->access_token;
    }
}
