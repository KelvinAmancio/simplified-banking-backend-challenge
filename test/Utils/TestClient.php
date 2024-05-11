<?php

namespace Test\Utils;

use Hyperf\Context\Context;
use Hyperf\Testing\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Coroutine;

class TestClient extends Client
{
    private array $connections = [];

    public function get(string $uri, array $data = [], array $headers = []): ?array
    {
        $result = parent::get($uri, $data, $headers);
        $this->waitCoroutines();
        return $result;
    }

    public function post(string $uri, array $data = [], array $headers = []): ?array
    {
        $result = parent::post($uri, $data, $headers);
        $this->waitCoroutines();
        return $result;
    }

    public function put(string $uri, array $data = [], array $headers = []): ?array
    {
        $result = parent::put($uri, $data, $headers);
        $this->waitCoroutines();
        return $result;
    }

    public function delete(string $uri, array $data = [], array $headers = []): ?array
    {
        $result = parent::delete($uri, $data, $headers);
        $this->waitCoroutines();
        return $result;
    }

    public function json(string $uri, array $data = [], array $headers = []): ?array
    {
        $result = parent::file($uri, $data, $headers);
        $this->waitCoroutines();
        return $result;
    }

    public function file(string $uri, array $data = [], array $headers = []): ?array
    {
        $result = parent::file($uri, $data, $headers);
        $this->waitCoroutines();
        return $result;
    }

    public function request(string $method, string $path, array $options = [], ?callable $callable = null): ResponseInterface
    {
        $result = parent::request($method, $path, $options, $callable);
        $this->waitCoroutines();
        return $result;
    }

    public function sendRequest(ServerRequestInterface $psr7Request, ?callable $callable = null): ResponseInterface
    {
        $result = parent::sendRequest($psr7Request, $callable);
        $this->waitCoroutines();
        return $result;
    }

    public function setDbConnection(array $connections): void
    {
        $this->connections = $connections;
    }

    public function setWaitTimeout(float $timeout): void
    {
        $this->waitTimeout = $timeout;
    }

    protected function persistToContext(ServerRequestInterface $request, ResponseInterface $response): void
    {
        parent::persistToContext($request, $response);

        foreach ($this->connections as $poolName => $connection) {
            $id = $this->getContextKey($poolName);
            Context::set($id, $connection);
        }
    }

    private function waitCoroutines(): void
    {
        while (Coroutine::stats()['coroutine_num'] > 1) {
            Coroutine::sleep(0.001);
        }
    }

    private function getContextKey(mixed $name): string
    {
        return sprintf('database.connection.%s', $name);
    }
}
