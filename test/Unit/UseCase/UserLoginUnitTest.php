<?php

declare(strict_types=1);

namespace Test\Unit\UseCase;

use App\Exception\BusinessException;
use App\Model\User;
use App\Service\Auth;
use App\Service\Db\UserService;
use App\Service\JwtWrapper;
use App\UseCase\UserLogin;
use Hyperf\Database\Model\ModelNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserLoginUnitTest extends TestCase
{
    /** @var MockObject&Auth */
    private MockObject $mockedAuth;

    /** @var MockObject&JwtWrapper */
    private MockObject $mockedJwtWrapper;

    /** @var MockObject&UserService */
    private MockObject $mockedUserService;

    private UserLogin $userLogin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedAuth = $this->createMock(Auth::class);
        $this->mockedJwtWrapper = $this->createMock(JwtWrapper::class);
        $this->mockedUserService = $this->createMock(UserService::class);

        $this->userLogin = new UserLogin(
            $this->mockedAuth,
            $this->mockedJwtWrapper,
            $this->mockedUserService
        );
    }

    public function testUserLoginExecuteUserNotFoundError()
    {
        $userData = [
            'email' => 'email@email.com',
            'password' => '123456abc'
        ];

        $userModelClass = User::class;

        $exception = (new ModelNotFoundException())->setModel($userModelClass);

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('findByEmailOrFail')
            ->with($userData['email'])
            ->willThrowException($exception);

        $this->mockedAuth->expects($this->never())->method('verifyPassword');

        $this->mockedJwtWrapper->expects($this->never())->method('encode');

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("No query results for model [{$userModelClass}]");

        $this->userLogin->execute($userData);
    }

    public function testUserLoginExecuteInvalidPasswordError()
    {
        $userData = [
            'email' => 'email@email.com',
            'password' => '123456abc'
        ];

        $savedUserData = [
            'password' => 'hashed_password'
        ];

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('findByEmailOrFail')
            ->with($userData['email'])
            ->willReturn($savedUserData);

        $this
            ->mockedAuth
            ->expects($this->once())
            ->method('verifyPassword')
            ->with($userData['password'], $savedUserData['password'])
            ->willReturn(false);

        $this->mockedJwtWrapper->expects($this->never())->method('encode');

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Não foi possível fazer login');

        $this->userLogin->execute($userData);
    }

    public function testUserLoginExecuteUserTypePfSuccess()
    {
        $userData = [
            'email' => 'email@email.com',
            'password' => '123456abc'
        ];

        $savedUserData = [
            'uuid' => uniqid(),
            'cpf_cnpj' => '111.111.111-11',
            'password' => 'hashed_password',
        ];

        $tokenData = [
            'user_uuid' => $savedUserData['uuid'],
            'user_type' => UserService::TYPE_PF
        ];

        $fakeToken = uniqid();

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('findByEmailOrFail')
            ->with($userData['email'])
            ->willReturn($savedUserData);

        $this
            ->mockedAuth
            ->expects($this->once())
            ->method('verifyPassword')
            ->with($userData['password'], $savedUserData['password'])
            ->willReturn(true);

        $this
            ->mockedJwtWrapper
            ->expects($this->once())
            ->method('encode')
            ->with($tokenData)
            ->willReturn($fakeToken);

        $result = $this->userLogin->execute($userData);

        $this->assertEquals(['token' => $fakeToken], $result);
    }

    public function testUserLoginExecuteUserTypePjSuccess()
    {
        $userData = [
            'email' => 'email@email.com',
            'password' => '123456abc'
        ];

        $savedUserData = [
            'uuid' => uniqid(),
            'cpf_cnpj' => '11.111.111/1111-11',
            'password' => 'hashed_password',
        ];

        $tokenData = [
            'user_uuid' => $savedUserData['uuid'],
            'user_type' => UserService::TYPE_PJ
        ];

        $fakeToken = uniqid();

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('findByEmailOrFail')
            ->with($userData['email'])
            ->willReturn($savedUserData);

        $this
            ->mockedAuth
            ->expects($this->once())
            ->method('verifyPassword')
            ->with($userData['password'], $savedUserData['password'])
            ->willReturn(true);

        $this
            ->mockedJwtWrapper
            ->expects($this->once())
            ->method('encode')
            ->with($tokenData)
            ->willReturn($fakeToken);

        $result = $this->userLogin->execute($userData);

        $this->assertEquals(['token' => $fakeToken], $result);
    }
}
