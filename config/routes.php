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
use App\Controller\IndexController;
use App\Controller\UserLoginController;
use App\Controller\UserRegisterController;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', IndexController::class);

Router::addRoute('POST', '/register', UserRegisterController::class);
Router::addRoute('POST', '/login', UserLoginController::class);

Router::get('/favicon.ico', fn () => '');
