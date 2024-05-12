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

namespace App\Listener;

use App\Event\TransferReceivedEvent;
use App\Model\Transfer;
use App\Service\TransferReceivedNotifier;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;

#[Listener]
class TransferReceivedListener implements ListenerInterface
{
    private TransferReceivedNotifier $transferReceivedNotifier;

    public function __construct(ContainerInterface $container)
    {
        $this->transferReceivedNotifier = $container->get(TransferReceivedNotifier::class);
    }

    public function listen(): array
    {
        return [
            TransferReceivedEvent::class
        ];
    }

    public function process(object $event): void
    {
        $transferData = $event->transferData;

        $notificationResult = $this->transferReceivedNotifier->execute($transferData);

        Transfer
            ::query()
            ->find($transferData['uuid'])
            ->update(['notification_sent' => !empty($notificationResult)]);
    }
}
