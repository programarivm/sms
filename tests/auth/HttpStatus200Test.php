<?php

namespace Tests\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HttpStatus200Test extends WebTestCase
{
    /**
     * @dataProvider data
     * @test
     */
    public function http_status_200($username, $password)
    {
        $user = [
            'username' => $username,
            'password' => $password
        ];

        $client = static::createClient();

        $client->request(
            'POST',
            '/api/auth',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($user)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function data()
    {
        $data = [];
        $users = json_decode(file_get_contents(__DIR__ . '/data/http_status_200.json'))->httpBody;
        foreach ($users as $user) {
            $data[] = [
                $user->username,
                $user->password
            ];
        }

        return $data;
    }
}
