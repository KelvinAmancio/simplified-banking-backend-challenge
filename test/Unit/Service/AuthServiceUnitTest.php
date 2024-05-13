<?php

declare(strict_types=1);

namespace Test\Unit\Service;

use App\Service\Auth;
use PHPUnit\Framework\TestCase;

class AuthServiceUnitTest extends TestCase
{
    private Auth $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = new Auth();
    }

    public function testHashPasswordSuccess()
    {
        $password = '123abc';

        $result = $this->authService->hashPassword($password);

        $isVerifiedPassword = password_verify($password, $result);

        $this->assertTrue($isVerifiedPassword);
    }

    public function testVerifyPasswordSuccess()
    {
        $password = '123abc';

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $result = $this->authService->verifyPassword($password, $hashedPassword);

        $this->assertTrue($result);
    }
}
