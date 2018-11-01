<?php

namespace Tests\Message\Create;

use Tests\TokenAuthenticatedWebTestCase;

class HttpStatus422Test extends TokenAuthenticatedWebTestCase
{
    /**
     * @dataProvider data
     * @test
     */
    public function http_status_422($telephone, $content)
    {
        $message = [
            'telephone' => $telephone,
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
            json_encode($message)
        );

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function data()
    {
        $data = [];
        $messages = json_decode(file_get_contents(__DIR__.'/data/http_status_422.json'))->httpBody;
        foreach ($messages as $message) {
            $data[] = [
                $message->telephone,
                $message->content,
            ];
        }

        return $data;
    }
}
