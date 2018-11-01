<?php

namespace Tests\Message\Listing;

use Tests\TokenAuthenticatedWebTestCase;

class HttpStatus200Test extends TokenAuthenticatedWebTestCase
{
    /**
     * @test
     */
    public function http_status_200()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/message/listing',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.self::$accessToken,
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
