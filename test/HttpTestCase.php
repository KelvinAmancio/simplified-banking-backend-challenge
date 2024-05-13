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

namespace Test;

use App\Helper\DbHelper;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\DbConnection\Db;
use Psr\EventDispatcher\ListenerProviderInterface;
use Swoole\Coroutine;
use Swoole\Table;
use Test\Utils\DbQueryExecutedTestHandlerTrait;
use Test\Utils\DbRestoreTableProvider;
use Test\Utils\TestClient;
use function Hyperf\Support\make;
use Hyperf\Testing\TestCase;

/**
 * Class HttpTestCase.
 * @method get($uri, $data = [], $headers = [])
 * @method post($uri, $data = [], $headers = [])
 * @method json($uri, $data = [], $headers = [])
 * @method file($uri, $data = [], $headers = [])
 * @method request($method, $path, $options = [])
 */
abstract class HttpTestCase extends TestCase
{
    use DbQueryExecutedTestHandlerTrait;
    use DbHelper;

    protected TestClient $client;

    protected Table $dbRestoreTable;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->client = make(TestClient::class);
        $this->dbRestoreTable = DbRestoreTableProvider::provide();
    }

    public function __call($name, $arguments)
    {
        return $this->client->{$name}(...$arguments);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $container = ApplicationContext::getContainer();

        $this->addDbRestoreConfigs($container);
        $this->client->setWaitTimeout(-1);

        $this->container = $container;
    }

    protected function tearDown(): void
    {
        $container = ApplicationContext::getContainer();

        $this->clearDatabaseFromTest($container);

        parent::tearDown();
    }

    private function addDbRestoreConfigs(ContainerInterface $container): void
    {
        $container->set('db_restore_table', $this->dbRestoreTable);

        $provider = $container->get(ListenerProviderInterface::class);
        $provider->on(QueryExecuted::class, function (QueryExecuted $event) use ($container) {
            $this->handle($event, $container);
        }, 1);
    }

    private function clearDatabaseFromTest(ContainerInterface $container): void
    {
        /* Get all pools */

        /** @var ConfigInterface $config */
        $config = $container->get(ConfigInterface::class);
        $databasesConfigs = $config->get('databases');
        $pools = array_keys($databasesConfigs);

        /* Check if there is any table to be cleared */
        if ($this->dbRestoreTable->count() === 0) {
            return;
        }

        /* Disable foreign key checks */
        foreach ($pools as $pool) {
            Db::connection($pool)->statement('SET FOREIGN_KEY_CHECKS = 0;');
        }

        /* Clear all tables */
        $this->dbRestoreTable->rewind();

        while ($this->dbRestoreTable->valid()) {
            $config = $this->dbRestoreTable->current();
            Db::connection($config['pool_name'])->delete($config['after_query']);
            $this->dbRestoreTable->next();
        }

        $this->dbRestoreTable = DbRestoreTableProvider::provide();

        /* Enable foreign key checks */
        foreach ($pools as $pool) {
            Db::connection($pool)->statement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }

    private function waitCoroutines(): void
    {
        while (Coroutine::stats()['coroutine_num'] > 1) {
            Coroutine::sleep(0.001);
        }
    }
}
