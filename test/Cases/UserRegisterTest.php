<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Test\Cases;

use App\Service\Auth;
use Test\HttpTestCase;

class UserRegisterTest extends HttpTestCase
{
    private Auth $auth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = $this->container->get(Auth::class);
    }

    public function testUserRegisterWithoutAnyFieldsError()
    {
        $userData = [];
        $expectedResponse = [
            'name' => ['validation.required'],
            'email' => ['validation.required'],
            'cpf_cnpj' => ['validation.required'],
            'password' => ['validation.required'],
        ];

        $resp = $this->post('/register', $userData);

        $decodedResponse = json_decode($resp->getContent(), true);
        $this->assertEquals(422, $resp->getStatusCode());
        $this->assertEquals($expectedResponse, $decodedResponse);
    }

    public function testUserRegisterSuccess()
    {
        $userData = [
            'name' => 'Kelvin Amancio',
            'email' => 'kelvi013@gmail.com',
            'cpf_cnpj' => '456.158.948-14',
            'password' => '123456abc',
        ];

        $resp = $this->post('/register', $userData);

        $decodedResponse = json_decode($resp->getContent(), true);
        $user = $decodedResponse['user'];
        $wallet = $decodedResponse['wallet'];

        // var_dump($decodedResponse);

        $this->assertEquals(200, $resp->getStatusCode());

        $this->assertEquals($userData['name'], $user['name']);
        $this->assertEquals($userData['email'], $user['email']);
        $this->assertEquals($userData['cpf_cnpj'], $user['cpf_cnpj']);

        $verifiedPassword = $this->auth->verifyPassword($userData['password'], $user['password']);
        $this->assertTrue($verifiedPassword);

        $this->assertEquals($user['uuid'], $wallet['owner_id']);
        $this->assertEquals(0, $wallet['balance']);
    }
}
