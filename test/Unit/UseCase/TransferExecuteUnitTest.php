<?php

declare(strict_types=1);

namespace Test\Unit\UseCase;

use App\Event\TransferReceivedEvent;
use App\Exception\BusinessException;
use App\Service\Db\TransferService;
use App\Service\Db\UserService;
use App\Service\Db\WalletService;
use App\Service\TransferAuthorizer;
use App\UseCase\TransferExecute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class TransferExecuteUnitTest extends TestCase
{
    /** @var MockObject&TransferAuthorizer */
    private MockObject $mockedTransferAuthorizer;

    /** @var MockObject&EventDispatcherInterface */
    private MockObject $mockedEventDispatcher;

    /** @var MockObject&TransferService */
    private MockObject $mockedTransferService;

    /** @var MockObject&UserService */
    private MockObject $mockedUserService;

    /** @var MockObject&WalletService */
    private MockObject $mockedWalletService;

    private TransferExecute $transferExecute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedTransferAuthorizer = $this->createMock(TransferAuthorizer::class);
        $this->mockedEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->mockedTransferService = $this->createMock(TransferService::class);
        $this->mockedUserService = $this->createMock(UserService::class);
        $this->mockedWalletService = $this->createMock(WalletService::class);

        $this->transferExecute = new TransferExecute(
            $this->mockedTransferAuthorizer,
            $this->mockedEventDispatcher,
            $this->mockedTransferService,
            $this->mockedUserService,
            $this->mockedWalletService
        );
    }

    public function testTransferExecuteMakeTransferInvalidPayerBalanceError()
    {
        $transferValue = 100;

        $payer = [
            'uuid' => uniqid(),
            'wallet' => ['balance' => 0]
        ];

        $payee = [
            'uuid' => uniqid(),
            'wallet' => ['balance' => 0]
        ];

        $transferData = [
            'value' => $transferValue,
            'payer' => $payer['uuid'],
            'payee' => $payee['uuid']
        ];

        $usersIds = [
            $transferData['payer'],
            $transferData['payee']
        ];

        $usersWithWallets = [
            [
                'uuid' => $payer['uuid'],
                'balance' => $payer['wallet']['balance']
            ],
            [
                'uuid' => $payee['uuid'],
                'balance' => $payee['wallet']['balance']
            ]
        ];

        $exceptionMessage = 'Balance unavailable for this transfer';

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('getWithWallets')
            ->with($usersIds)
            ->willReturn($usersWithWallets);

        $this->mockedWalletService->expects($this->never())->method('updateBalance');

        $this->mockedTransferAuthorizer->expects($this->never())->method('execute');

        $this->mockedTransferService->expects($this->never())->method('save');

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->transferExecute->makeTransfer($transferData);
    }

    public function testTransferExecuteMakeTransferUnauthorizedError()
    {
        $transferValue = 100;

        $payer = [
            'uuid' => uniqid(),
            'wallet' => ['balance' => $transferValue]
        ];

        $payee = [
            'uuid' => uniqid(),
            'wallet' => ['balance' => 0]
        ];

        $transferData = [
            'value' => $transferValue,
            'payer' => $payer['uuid'],
            'payee' => $payee['uuid']
        ];

        $usersIds = [
            $transferData['payer'],
            $transferData['payee']
        ];

        $usersWithWallets = [
            [
                'uuid' => $payer['uuid'],
                'balance' => $payer['wallet']['balance']
            ],
            [
                'uuid' => $payee['uuid'],
                'balance' => $payee['wallet']['balance']
            ]
        ];

        $exceptionMessage = 'Transfer not authorized';

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('getWithWallets')
            ->with($usersIds)
            ->willReturn($usersWithWallets);

        $this
            ->mockedWalletService
            ->expects($this->exactly(2))
            ->method('updateBalance');

        $this
            ->mockedTransferAuthorizer
            ->expects($this->once())
            ->method('execute')
            ->with($transferData);

        $this->mockedTransferService->expects($this->never())->method('save');

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->transferExecute->makeTransfer($transferData);
    }

    public function testTransferExecuteMakeTransferSuccess()
    {
        $transferValue = 100;

        $payer = [
            'uuid' => uniqid(),
            'wallet' => ['balance' => $transferValue]
        ];

        $payee = [
            'uuid' => uniqid(),
            'wallet' => ['balance' => 0]
        ];

        $transferData = [
            'value' => $transferValue,
            'payer' => $payer['uuid'],
            'payee' => $payee['uuid']
        ];

        $usersIds = [
            $transferData['payer'],
            $transferData['payee']
        ];

        $usersWithWallets = [
            [
                'uuid' => $payer['uuid'],
                'balance' => $payer['wallet']['balance']
            ],
            [
                'uuid' => $payee['uuid'],
                'balance' => $payee['wallet']['balance']
            ]
        ];

        $transferDataToSave = [
            'payer_id' => $payer['uuid'],
            'payee_id' => $payee['uuid'],
            'value' => $transferValue,
            'authorized' => true
        ];

        $savedTransferData = [
            'uuid' => uniqid(),
            ...$transferDataToSave
        ];

        $this
            ->mockedUserService
            ->expects($this->once())
            ->method('getWithWallets')
            ->with($usersIds)
            ->willReturn($usersWithWallets);

        $this
            ->mockedWalletService
            ->expects($this->exactly(2))
            ->method('updateBalance');

        $this
            ->mockedTransferAuthorizer
            ->expects($this->once())
            ->method('execute')
            ->with($transferData)
            ->willReturn(['authorized' => true]);

        $this
            ->mockedTransferService
            ->expects($this->once())
            ->method('save')
            ->with($transferDataToSave)
            ->willReturn($savedTransferData);

        $result = $this->transferExecute->makeTransfer($transferData);

        $this->assertEquals($savedTransferData, $result);
    }

    public function testTransferExecuteNotifyPayeeSuccess()
    {
        $transferData = [
            'value' => 100,
            'payer' => uniqid(),
            'payee' => uniqid()
        ];

        $this
            ->mockedEventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(new TransferReceivedEvent($transferData));

        $this->transferExecute->notifyPayee($transferData);
    }
}
