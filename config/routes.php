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
use App\Controller\TransferExecuteController;
use App\Controller\UserLoginController;
use App\Controller\UserRegisterController;
use App\Middleware\AllowedToTransferMiddleware;
use App\Middleware\Auth\LoggedInUserMiddleware;
use Hyperf\HttpServer\Router\Router;

// cadastro e login de usuários
Router::addRoute('POST', '/register', UserRegisterController::class);
Router::addRoute('POST', '/login', UserLoginController::class);

// rotas apenas para usuários logados
Router::addGroup(
    '', function () {
        // efetuar transferências
        Router::addRoute(
            'POST',
            '/transfer',
            TransferExecuteController::class,
            ['middleware' => [AllowedToTransferMiddleware::class]]
        );

        // listar transferências
        // Router::addRoute('GET', '/summary', IndexController::class);

        // obter dados da carteira
        // Router::addRoute('GET', '/wallet', IndexController::class);
    },
    ['middleware' => [LoggedInUserMiddleware::class]]
);


Router::get('/favicon.ico', fn () => '');
