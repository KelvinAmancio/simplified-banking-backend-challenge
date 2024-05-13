<?php

declare(strict_types=1);

namespace Test\Unit\Service;

use App\Service\TransferAuthorizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TransferAuthorizerUnitTest extends TestCase
{
    /** @var MockObject&LoggerInterface */
    private MockObject $mockedLoggerInterface;

    private TransferAuthorizer $transferAuthorizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedLoggerInterface = $this->createMock(LoggerInterface::class);

        $this->transferAuthorizer = new TransferAuthorizer('', $this->mockedLoggerInterface);
    }

    public function testExecuteTransferAuthorizer()
    {
        $result = $this->transferAuthorizer->execute([]);

        $this->assertEquals([], $result);
    }
}
