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

namespace App\Controller;

use App\Request\TransferExecuteRequest as Request;
use App\UseCase\TransferExecute;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;

class TransferExecuteController extends AbstractController
{
    public function __construct(private TransferExecute $transferExecute)
    {
    }

    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $payerAuth = $request->getAttribute('auth');

        $validatedTransfer = $request->validated();
        $validatedTransfer['payer'] = $payerAuth['user_uuid'];

        $savedTransfer = $this->transferExecute->execute($validatedTransfer);

        return $response->json($savedTransfer);
    }
}
