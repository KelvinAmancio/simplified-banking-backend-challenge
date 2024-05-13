<?php

declare(strict_types=1);

namespace Test\Unit\Controller;

use App\Controller\TransferExecuteController;
use App\UseCase\TransferExecute;
use App\Request\TransferExecuteRequest as Request;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class TransferExecuteControllerUnitTest extends TestCase
{
    /** @var MockObject&TransferExecute */
    private MockObject $mockedTransferExecute;

    private TransferExecuteController $transferExecuteController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedTransferExecute = $this->createMock(TransferExecute::class);

        $this->transferExecuteController = new TransferExecuteController(
            $this->mockedTransferExecute
        );
    }

    public function testTransferExecuteControllerInvokeSuccess()
    {
        $authData = ['user_uuid' => uniqid()];
        $validatedTransfer = ['payer' => $authData['user_uuid']];

        $mockedRequest = $this->createMock(Request::class);
        $mockedResponse = $this->createMock(Response::class);
        $mockedResponseInterface = $this->createMock(ResponseInterface::class);

        $mockedRequest
            ->expects($this->once())
            ->method('getAttribute')
            ->with('auth')
            ->willReturn($authData);

        $mockedRequest
            ->expects($this->once())
            ->method('validated')
            ->willReturn([]);

        $this
            ->mockedTransferExecute
            ->expects($this->once())
            ->method('execute')
            ->with($validatedTransfer)
            ->willReturn([]);

        $mockedResponse
            ->expects($this->once())
            ->method('json')
            ->willReturn($mockedResponseInterface);

        $result = $this->transferExecuteController->__invoke(
            $mockedRequest,
            $mockedResponse
        );

        $this->assertEquals($mockedResponseInterface, $result);
    }
}
