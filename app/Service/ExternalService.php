<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use Hyperf\Coroutine\Coroutine;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\PoolHandler;
use Hyperf\Guzzle\RetryMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use function Hyperf\Support\make;

abstract class ExternalService
{
    protected Client $client;

    public function __construct(
        private string $baseUri,
        private LoggerInterface $logger
    ) {
        $this->client = $this->makeHttpClient($this->baseUri);
    }

    public abstract function execute(array $payload): array;

    public function post(string $uri, array $payload): array
    {
        try {
            $response = $this->client->post($uri, $payload);
            return $this->decodeResponse($response);
        } catch (\Throwable $th) {
            $this->logger->error($th->getMessage());
            return [];
        }
    }

    protected function decodeResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    private function makeHttpClient(string $baseUri): Client
    {
        $poolHandlerOptions = ['max_connections' => 50];

        $handler = Coroutine::inCoroutine()
            ? make(PoolHandler::class, ['option' => $poolHandlerOptions])
            : null;

        $retry = make(RetryMiddleware::class, [
            'retries' => 1,
            'delay' => 10
        ]);

        $stack = HandlerStack::create($handler);
        $stack->push($retry->getMiddleware(), 'retry');

        return make(Client::class, [
            'config' => [
                'handler' => $stack,
                'base_uri' => $baseUri
            ]
        ]);
    }
}
