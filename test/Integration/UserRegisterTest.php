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

namespace Test\Integration;

use App\Model\User;
use App\Service\Auth;
use App\Service\JwtWrapper;
use Test\HttpTestCase;

class UserRegisterTest extends HttpTestCase
{
    private Auth $auth;
    private JwtWrapper $jwtWrapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = $this->container->get(Auth::class);
        $this->jwtWrapper = $this->container->get(JwtWrapper::class);
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
        $this->assertEquals($expectedResponse, $decodedResponse['details']);
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
        $token = $decodedResponse['token'];
        $wallet = $decodedResponse['wallet'];

        $this->assertEquals(200, $resp->getStatusCode());

        $this->assertEquals($userData['name'], $user['name']);
        $this->assertEquals($userData['email'], $user['email']);
        $this->assertEquals($userData['cpf_cnpj'], $user['cpf_cnpj']);

        $verifiedPassword = $this->auth->verifyPassword($userData['password'], $user['password']);
        $this->assertTrue($verifiedPassword);

        $jwtInfo = $this->jwtWrapper->decode($token);
        $this->assertEquals($user['uuid'], $jwtInfo->user_uuid);
        $this->assertEquals(User::TYPE_PF, $jwtInfo->user_type);

        $this->assertEquals($user['uuid'], $wallet['owner_id']);
        $this->assertEquals(0, $wallet['balance']);
    }
}
