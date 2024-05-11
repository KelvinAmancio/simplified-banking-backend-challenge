<?php

declare(strict_types=1);

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtWrapper
{
    private int $issuedAt;

    public function __construct(
        private string $secret,
        private int $duration,
        int $issuedAtOffset = 0
    ) {
        $this->issuedAt = time() + $issuedAtOffset;
    }

    public function encode(array $info = [], int $customDuration = null): string
    {
        $tokenParams = [
            'iat' => $this->issuedAt,
            'exp' => $this->issuedAt + ($customDuration ?? $this->duration),
            'nbf' => $this->issuedAt - 1,
            'data' => $info,
        ];

        return JWT::encode($tokenParams, $this->secret, 'HS256');
    }

    public function decode(string $jwtToken): ?\stdClass
    {
        try {
            return JWT::decode($jwtToken, new Key($this->secret, 'HS256'))->data;
        } catch (\Exception) {
            return null;
        }
    }
}
