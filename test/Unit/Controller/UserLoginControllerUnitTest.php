<?php

declare(strict_types=1);

namespace Test\Unit\Controller;

use App\Controller\UserLoginController;
use App\Request\UserLoginRequest as Request;
use App\UseCase\UserLogin;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class UserLoginControllerUnitTest extends TestCase
{
    /** @var MockObject&UserLogin */
    private MockObject $mockedUserLogin;

    private UserLoginController $userLoginController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedUserLogin = $this->createMock(UserLogin::class);

        $this->userLoginController = new UserLoginController($this->mockedUserLogin);
    }

    public function testTransferExecuteControllerInvokeSuccess()
    {
        $validatedLoginData = ['email' => 'email', 'senha' => 'senha'];

        $mockedRequest = $this->createMock(Request::class);
        $mockedResponse = $this->createMock(Response::class);
        $mockedResponseInterface = $this->createMock(ResponseInterface::class);

        $mockedRequest
            ->expects($this->once())
            ->method('validated')
            ->willReturn($validatedLoginData);

        $this
            ->mockedUserLogin
            ->expects($this->once())
            ->method('execute')
            ->with($validatedLoginData)
            ->willReturn([]);

        $mockedResponse
            ->expects($this->once())
            ->method('json')
            ->with([])
            ->willReturn($mockedResponseInterface);

        $result = $this->userLoginController->__invoke(
            $mockedRequest,
            $mockedResponse
        );

        $this->assertEquals($mockedResponseInterface, $result);
    }
}
