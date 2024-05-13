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

use App\Request\UserLoginRequest as Request;
use App\UseCase\UserLogin;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;

class UserLoginController extends AbstractController
{
    public function __construct(private UserLogin $userLogin)
    {
    }

    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $validatedLoginData = $request->validated();

        $savedLoginData = $this->userLogin->execute($validatedLoginData);

        return $response->json($savedLoginData);
    }
}
