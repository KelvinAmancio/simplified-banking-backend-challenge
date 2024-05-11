<?php

declare(strict_types=1);

namespace Test\Utils;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Database\Events\QueryExecuted;
use Swoole\Table;

trait DbQueryExecutedTestHandlerTrait
{
    public function handle(QueryExecuted $event, ContainerInterface $container): void
    {
        preg_match('/^(?i)insert into \`?(\w+)\`? *\(.+\) values *\(.+\)(?-i)/', $event->sql, $matches);
        if (count($matches) > 0) {
            $tableName = $matches[1];

            $query = 'DELETE FROM %s WHERE uuid = "%s"';
            $lastInsertedQuery = "SELECT uuid FROM {$tableName} ORDER BY uuid DESC LIMIT 1";
            $lastInserted = $event->connection->getPdo()->query($lastInsertedQuery)->fetch(\PDO::FETCH_ASSOC);
            $query = sprintf($query, $tableName, $lastInserted['uuid']);

            /** @var Table $dbRestoreTestTable */
            $dbRestoreTestTable = $container->get('db_restore_table');
            $dbRestoreTestTable->set((string) $dbRestoreTestTable->count(), [
                'after_query' => $query,
                'pool_name' => $event->connectionName
            ]);

            $rows = $this->getInsertRows($event->sql);
            if ($rows > 1) {
                $this->handleMultipleRows($rows, $tableName, $event, $container);
            }
        }
    }

    private function getInsertRows(string $sql): int
    {
        $inserts = explode('values', $sql);
        $rows = explode('),', $inserts[sizeof($inserts) - 1]);

        return sizeof($rows);
    }

    private function handleMultipleRows(int $rows, string $tableName, QueryExecuted $event, ContainerInterface $container): void
    {
        $lastInsertId = intval($event->connection->getPdo()->lastInsertId('uuid'));
        $rows = $rows - 1;

        for ($i = 1; $i <= $rows; $i++) {
            $insertId = $lastInsertId + $i;

            $query = 'DELETE FROM %s WHERE uuid = "%s"';
            $query = sprintf($query, $tableName, $insertId);

            $dbRestoreTestTable = $container->get('db_restore_table');
            $dbRestoreTestTable->set((string)$dbRestoreTestTable->count(), [
                'after_query' => $query,
                'pool_name' => $event->connectionName
            ]);
        }
    }
}
