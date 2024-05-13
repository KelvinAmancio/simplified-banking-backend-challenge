<?php

declare(strict_types=1);

namespace App\Helper;

use Hyperf\Stringable\Str;

trait DbHelper
{
    protected function buildUuid()
    {
        return strtolower((string) Str::ulid());
    }
}
