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

use App\Model\Transfer;
use App\Model\User;
use App\Model\Wallet;
use App\Service\Auth;
use App\Service\Db\UserService;
use App\Service\JwtWrapper;
use App\Service\TransferAuthorizer;
use App\Service\TransferReceivedNotifier;
use PHPUnit\Framework\MockObject\MockObject;
use Test\HttpTestCase;

class TransferExecuteTest extends HttpTestCase
{
    private Auth $auth;

    private JwtWrapper $jwtWrapper;

    /** @var MockObject&TransferAuthorizer */
    private MockObject $mockedTransferAuthorizer;

    /** @var MockObject&TransferReceivedNotifier */
    private MockObject $mockedTransferReceivedNotifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedTransferAuthorizer = $this->createMock(TransferAuthorizer::class);
        $this->container->set(TransferAuthorizer::class, $this->mockedTransferAuthorizer);

        $this->auth = $this->container->get(Auth::class);
        $this->jwtWrapper = $this->container->get(JwtWrapper::class);
    }

    public function testTransferExecuteWithoutAnyFieldsError()
    {
        $userData = [];
        $expectedResponse = [
            'value' => ['validation.required'],
            'payee' => ['validation.required'],
        ];

        $this
            ->mockedTransferAuthorizer
            ->expects($this->never())
            ->method('execute');

        $resp = $this->post('/transfer', $userData);

        $decodedResponse = json_decode($resp->getContent(), true);
        $this->assertEquals(422, $resp->getStatusCode());
        $this->assertEquals($expectedResponse, $decodedResponse['details']);
    }

    public function testTransferExecuteSuccess()
    {
        $payerData = [
            'name' => 'Payer User',
            'email' => 'payer@email.com',
            'cpf_cnpj' => '111.111.111-11',
            'password' => '123456abc',
        ];

        $payeeData = [
            'name' => 'Payee User',
            'email' => 'payee@email.com',
            'cpf_cnpj' => '222.222.222-22',
            'password' => '123456abc',
        ];

        $transferValue = 100;

        $payer = $this->createUserWithWallet($payerData);

        $payee = $this->createUserWithWallet($payeeData);

        $transferData = [
            'value' => $transferValue,
            'payee' => $payee['uuid'],
        ];

        $this
            ->mockedTransferAuthorizer
            ->expects($this->once())
            ->method('execute')
            ->with([...$transferData, 'payer' => $payer['uuid']])
            ->willReturn(['authorized' => true]);

        $headers = ['Authorization' => $this->buildUserToken($payer)];

        $resp = $this->post('/transfer', $transferData, $headers);

        $decodedResponse = json_decode($resp->getContent(), true);

        $transferBeforeNotification = $decodedResponse['transfer'];

        $this->assertEquals(200, $resp->getStatusCode());

        $this->assertEquals($payer['uuid'], $transferBeforeNotification['payer_id']);
        $this->assertEquals($payee['uuid'], $transferBeforeNotification['payee_id']);
        $this->assertEquals($transferValue, $transferBeforeNotification['value']);
        $this->assertEquals(1, $transferBeforeNotification['authorized']);
        $this->assertEquals(0, $transferBeforeNotification['notification_sent']);

        $transferAfterNotification = Transfer::find($transferBeforeNotification['uuid'])->toArray();
        $this->assertEquals(0, $transferAfterNotification['notification_sent']);
    }

    private function createUserWithWallet(array $userData): array
    {
        $userAttributes = [
            ...$userData,
            'uuid' => self::buildUuid(),
            'password' => $this->auth->hashPassword($userData['password'])
        ];

        $user = User::create($userAttributes)->toArray();

        $walletData = [
            'uuid' => self::buildUuid(),
            'owner_id' => $user['uuid'],
            'balance' => 1000
        ];

        Wallet::create($walletData)->toArray();

        return $user;
    }

    private function buildUserToken(array $userData): string
    {
        $tokenData = [
            'user_uuid' => $userData['uuid'],
            'user_type' => UserService::getType($userData['cpf_cnpj'])
        ];

        return 'Bearer ' . $this->jwtWrapper->encode($tokenData);
    }
}
