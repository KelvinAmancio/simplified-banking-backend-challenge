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

use App\Request\UserRegisterRequest as Request;
use App\UseCase\UserRegister;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface as ResponseInterface;

class UserRegisterController extends AbstractController
{
    public function __construct(private UserRegister $userRegister)
    {
    }

    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $validatedUserData = $request->validated();

        $savedUserData = $this->userRegister->execute($validatedUserData);

        return $response->json($savedUserData);
    }
}
