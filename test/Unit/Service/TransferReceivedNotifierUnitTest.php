<?php

declare(strict_types=1);

namespace Test\Unit\Service;

use App\Service\TransferReceivedNotifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TransferReceivedNotifierUnitTest extends TestCase
{
    /** @var MockObject&LoggerInterface */
    private MockObject $mockedLoggerInterface;

    private TransferReceivedNotifier $transferReceivedNotifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedLoggerInterface = $this->createMock(LoggerInterface::class);

        $this->transferReceivedNotifier = new TransferReceivedNotifier(
            '',
            $this->mockedLoggerInterface
        );
    }

    public function testExecuteTransferReceivedNotifier()
    {
        $result = $this->transferReceivedNotifier->execute([]);

        $this->assertEquals([], $result);
    }
}
