<?php

declare(strict_types=1);

namespace Test\Unit\Listener;

use App\Event\TransferReceivedEvent;
use App\Listener\TransferReceivedListener;
use App\Service\Db\TransferService;
use App\Service\TransferReceivedNotifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class TransferReceivedListenerUnitTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private MockObject $mockedContainerInterface;

    /** @var MockObject&TransferReceivedNotifier */
    private MockObject $mockedTransferReceivedNotifier;

    /** @var MockObject&TransferService */
    private MockObject $mockedTransferService;

    private TransferReceivedListener $transferReceivedListener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedContainerInterface = $this->createMock(ContainerInterface::class);
        $this->mockedTransferReceivedNotifier = $this->createMock(TransferReceivedNotifier::class);
        $this->mockedTransferService = $this->createMock(TransferService::class);

        $this
            ->mockedContainerInterface
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                [TransferReceivedNotifier::class, $this->mockedTransferReceivedNotifier],
                [TransferService::class, $this->mockedTransferService],
            ]);

        $this->transferReceivedListener = new TransferReceivedListener(
            $this->mockedContainerInterface
        );
    }

    public function testListenSuccess()
    {
        $expectedResult = [
            TransferReceivedEvent::class
        ];

        $result = $this->transferReceivedListener->listen();
        $this->assertEquals($expectedResult, $result);
    }

    public function testProcessTransferReceivedNotifierError()
    {
        $transferData = [
            'uuid' => uniqid(),
        ];

        $event = (object) ['transferData' => $transferData];

        $this
            ->mockedTransferReceivedNotifier
            ->expects($this->once())
            ->method('execute')
            ->with($transferData);

        $this
            ->mockedTransferService
            ->expects($this->once())
            ->method('updateTransferNotificationSent')
            ->with($transferData['uuid'], []);

        $this->transferReceivedListener->process($event);
    }

    public function testProcessTransferReceivedNotifierSuccess()
    {
        $transferData = [
            'uuid' => uniqid(),
        ];

        $event = (object) ['transferData' => $transferData];

        $notificationResult = ['notification_sent' => true];

        $this
            ->mockedTransferReceivedNotifier
            ->expects($this->once())
            ->method('execute')
            ->with($transferData)
            ->willReturn($notificationResult);

        $this
            ->mockedTransferService
            ->expects($this->once())
            ->method('updateTransferNotificationSent')
            ->with($transferData['uuid'], $notificationResult);

        $this->transferReceivedListener->process($event);
    }
}
