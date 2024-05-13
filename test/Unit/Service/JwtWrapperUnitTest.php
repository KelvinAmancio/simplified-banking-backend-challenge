<?php

declare(strict_types=1);

namespace Test\Unit\Service;

use App\Service\JwtWrapper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPUnit\Framework\TestCase;

class JwtWrapperUnitTest extends TestCase
{
    private JwtWrapper $jwtWrapperService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtWrapperService = new JwtWrapper('secret', 1800);
    }

    public function testEncodeSuccess()
    {
        $data = ['info'];

        $result = $this->jwtWrapperService->encode($data);

        $decodedData = JWT::decode($result, new Key('secret', 'HS256'))->data;

        $this->assertEquals($data, $decodedData);
    }

    public function testDecodeSuccess()
    {
        $data = ['info' => 'info'];

        $tokenParams = [
            'iat' => time(),
            'exp' => time() + 1800,
            'nbf' => time() - 1,
            'data' => $data,
        ];

        $token = JWT::encode($tokenParams, 'secret', 'HS256');

        $result = $this->jwtWrapperService->decode($token);

        $this->assertEquals((object) $data, $result);
    }

    public function testDecodeError()
    {
        $invalidToken = 'invalid_token';

        $result = $this->jwtWrapperService->decode($invalidToken);

        $this->assertNull($result);
    }
}
