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
use App\Service\Db\UserService;
use App\Service\JwtWrapper;
use Test\HttpTestCase;

class UserLoginTest extends HttpTestCase
{
    private Auth $auth;
    private JwtWrapper $jwtWrapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = $this->container->get(Auth::class);
        $this->jwtWrapper = $this->container->get(JwtWrapper::class);
    }

    public function testUserLoginWithoutAnyFieldsError()
    {
        $userData = [];
        $expectedResponse = [
            'email' => ['validation.required'],
            'password' => ['validation.required'],
        ];

        $resp = $this->post('/login', $userData);

        $decodedResponse = json_decode($resp->getContent(), true);
        $this->assertEquals(422, $resp->getStatusCode());
        $this->assertEquals($expectedResponse, $decodedResponse['details']);
    }

    public function testUserLoginSuccess()
    {
        $userData = [
            'name' => 'Kelvin Amancio',
            'email' => 'kelvi013@gmail.com',
            'cpf_cnpj' => '456.158.948-14',
            'password' => '123456abc',
        ];

        $userLoginData = [
            'email' => $userData['email'],
            'password' => $userData['password'],
        ];

        $user = $this->createUser($userData);

        $resp = $this->post('/login', $userLoginData);

        $decodedResponse = json_decode($resp->getContent(), true);
        $token = $decodedResponse['token'];

        $this->assertEquals(200, $resp->getStatusCode());

        $jwtInfo = $this->jwtWrapper->decode($token);
        $this->assertEquals($user['uuid'], $jwtInfo->user_uuid);
        $this->assertEquals(UserService::TYPE_PF, $jwtInfo->user_type);
    }

    private function createUser(array $userData): array
    {
        $userAttributes = [
            ...$userData,
            'uuid' => self::buildUuid(),
            'password' => $this->auth->hashPassword($userData['password'])
        ];

        return User::create($userAttributes)->toArray();
    }
}
