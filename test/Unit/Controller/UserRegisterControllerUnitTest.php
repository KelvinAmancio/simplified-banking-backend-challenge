<?php

declare(strict_types=1);

namespace Test\Unit\Controller;

use App\Controller\UserRegisterController;
use App\Request\UserRegisterRequest as Request;
use App\UseCase\UserRegister;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class UserRegisterControllerUnitTest extends TestCase
{
    /** @var MockObject&UserRegister */
    private MockObject $mockedUserRegister;

    private UserRegisterController $userRegisterController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedUserRegister = $this->createMock(UserRegister::class);

        $this->userRegisterController = new UserRegisterController($this->mockedUserRegister);
    }

    public function testTransferExecuteControllerInvokeSuccess()
    {
        $validatedRegisterData = ['email' => 'email', 'senha' => 'senha'];

        $mockedRequest = $this->createMock(Request::class);
        $mockedResponse = $this->createMock(Response::class);
        $mockedResponseInterface = $this->createMock(ResponseInterface::class);

        $mockedRequest
            ->expects($this->once())
            ->method('validated')
            ->willReturn($validatedRegisterData);

        $this
            ->mockedUserRegister
            ->expects($this->once())
            ->method('execute')
            ->with($validatedRegisterData)
            ->willReturn([]);

        $mockedResponse
            ->expects($this->once())
            ->method('json')
            ->with([])
            ->willReturn($mockedResponseInterface);

        $result = $this->userRegisterController->__invoke(
            $mockedRequest,
            $mockedResponse
        );

        $this->assertEquals($mockedResponseInterface, $result);
    }
}
