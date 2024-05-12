<?php

declare(strict_types=1);

namespace Test\Unit\UseCase;

use App\Model\User;
use App\Service\Auth;
use App\Service\Db\UserService;
use App\Service\Db\WalletService;
use App\Service\JwtWrapper;
use App\UseCase\UserRegister;
use Hyperf\Database\Model\ModelNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserRegisterUnitTest extends TestCase
{
    /** @var MockObject&Auth */
    private MockObject $mockedAuth;

    /** @var MockObject&JwtWrapper */
    private MockObject $mockedJwtWrapper;

    /** @var MockObject&UserService */
    private MockObject $mockedUserService;

    /** @var MockObject&WalletService */
    private MockObject $mockedWalletService;

    private UserRegister $userRegister;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedAuth = $this->createMock(Auth::class);
        $this->mockedJwtWrapper = $this->createMock(JwtWrapper::class);
        $this->mockedUserService = $this->createMock(UserService::class);
        $this->mockedWalletService = $this->createMock(WalletService::class);

        $this->userRegister = new UserRegister(
            $this->mockedAuth,
            $this->mockedJwtWrapper,
            $this->mockedUserService,
            $this->mockedWalletService,
        );
    }

    public function testUserRegisterExecuteUserPfSuccess()
    {
        $userData = [
            'email' => 'email@email.com',
            'cpf_cnpj' => '111.111.111-11',
            'password' => '123456abc'
        ];

        $userDataToSave = [
            'email' => $userData['email'],
            'cpf_cnpj' => $userData['cpf_cnpj'],
            'password' => 'hashed_password',
        ];

        $savedUserData = [
            'uuid' => uniqid(),
            ...$userDataToSave
        ];

        $tokenData = [
            'user_uuid' => $savedUserData['uuid'],
            'user_type' => UserService::TYPE_PF
        ];

        $fakeToken = uniqid();

        $walletData = [
            'owner_id' => $savedUserData['uuid']
        ];

        $savedWalletData = [
            'uuid' => uniqid(),
            'owner_id' => $savedUserData['uuid']
        ];

        $this
            ->mockedAuth
            ->expects($this->once())
            ->method('hashPassword')
            ->with($userData['password'])
            ->willReturn($userDataToSave['password']);

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('save')
            ->with($userDataToSave)
            ->willReturn($savedUserData);

        $this
            ->mockedJwtWrapper
            ->expects($this->once())
            ->method('encode')
            ->with($tokenData)
            ->willReturn($fakeToken);

        $this
            ->mockedWalletService
            ->expects($this->once())
            ->method('save')
            ->with($walletData)
            ->willReturn($savedWalletData);

        $result = $this->userRegister->execute($userData);

        $this->assertEquals($fakeToken, $result['token']);
        $this->assertEquals($savedUserData, $result['user']);
        $this->assertEquals($savedWalletData, $result['wallet']);
    }

    public function testUserRegisterExecuteUserPjSuccess()
    {
        $userData = [
            'email' => 'email@email.com',
            'cpf_cnpj' => '11.111.111/1111-11',
            'password' => '123456abc'
        ];

        $userDataToSave = [
            'email' => $userData['email'],
            'cpf_cnpj' => $userData['cpf_cnpj'],
            'password' => 'hashed_password',
        ];

        $savedUserData = [
            'uuid' => uniqid(),
            ...$userDataToSave
        ];

        $tokenData = [
            'user_uuid' => $savedUserData['uuid'],
            'user_type' => UserService::TYPE_PJ
        ];

        $fakeToken = uniqid();

        $walletData = [
            'owner_id' => $savedUserData['uuid']
        ];

        $savedWalletData = [
            'uuid' => uniqid(),
            'owner_id' => $savedUserData['uuid']
        ];

        $this
            ->mockedAuth
            ->expects($this->once())
            ->method('hashPassword')
            ->with($userData['password'])
            ->willReturn($userDataToSave['password']);

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('save')
            ->with($userDataToSave)
            ->willReturn($savedUserData);

        $this
            ->mockedJwtWrapper
            ->expects($this->once())
            ->method('encode')
            ->with($tokenData)
            ->willReturn($fakeToken);

        $this
            ->mockedWalletService
            ->expects($this->once())
            ->method('save')
            ->with($walletData)
            ->willReturn($savedWalletData);

        $result = $this->userRegister->execute($userData);

        $this->assertEquals($fakeToken, $result['token']);
        $this->assertEquals($savedUserData, $result['user']);
        $this->assertEquals($savedWalletData, $result['wallet']);
    }
}
