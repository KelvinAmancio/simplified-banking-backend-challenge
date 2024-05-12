<?php

declare(strict_types=1);

namespace App\Event;

class TransferReceivedEvent
{
    public function __construct(public array $transferData)
    {
    }
}
