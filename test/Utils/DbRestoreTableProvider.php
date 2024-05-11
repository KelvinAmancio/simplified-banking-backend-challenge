<?php

namespace Test\Utils;

use Swoole\Table;

class DbRestoreTableProvider
{
    public static function provide(): Table
    {
        $table = new Table(1024);
        $table->column('after_query', Table::TYPE_STRING, 1024);
        $table->column('pool_name', Table::TYPE_STRING, 64);
        $table->create();

        return $table;
    }
}