<?php

namespace Tests\Message\Create;

use Tests\TokenAuthenticatedWebTestCase;

class HttpStatus200Test extends TokenAuthenticatedWebTestCase
{
    /**
     * @dataProvider data
     * @test
     */
    public function http_status_200($content)
    {
        $message = [
            'content' => $content,
        ];

        $client = static::createClient();

        $client->request(
            'POST',
            '/api/message/send',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.self::$accessToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($content)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function data()
    {
        $data = [];
        $messages = json_decode(file_get_contents(__DIR__.'/data/http_status_200.json'))->httpBody;
        foreach ($messages as $message) {
            $data[] = [
                $message->content,
            ];
        }

        return $data;
    }
}
