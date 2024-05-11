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

use Test\HttpTestCase;

class UserRegisterTest extends HttpTestCase
{
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
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals($userData['name'], $decodedResponse['name']);
        $this->assertEquals($userData['email'], $decodedResponse['email']);
        $this->assertEquals($userData['cpf_cnpj'], $decodedResponse['cpf_cnpj']);
        $this->assertEquals($userData['password'], $decodedResponse['password']);
    }
}
