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

use App\Model\User;
use App\Request\UserRegisterRequest;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

class UserRegisterController extends AbstractController
{
    public function __invoke(
        UserRegisterRequest $request,
        ResponseInterface $response
    ): Psr7ResponseInterface {
        $validated = $request->validated();

        $user = new User($validated);
        $uuid = $user->newUniqueId();
        $user->setAttribute('uuid', $uuid);
        $user->save();

        return $response->json(User::query()->find($uuid)->toArray());
    }
}
