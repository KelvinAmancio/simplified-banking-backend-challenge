<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use App\Helper\DbHelper;
use PHPUnit\Framework\TestCase;
use Hyperf\Stringable\Str;

class DbHelperUnitTest extends TestCase
{
    use DbHelper;

    public function testBuildUuidSuccess()
    {
        $result = $this->buildUuid();
        $this->assertTrue(Str::isUlid($result));
    }
}
