<?php

namespace App\EventListener\Workflow;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\Workflow\BackendEvent as Event;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class AuditTrailListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onSetting(Event $event)
    {
        foreach ($event->getTransition()->getFroms() as $place) {
            $markingId = $event->getMarking()->getMarkingId();
            $storeId = $event->getStoreId();
            $this->logger->info(sprintf('Setting marking "%s" to store "%s".', $markingId, $storeId));
        }
    }

    public function onNewStore(Event $event)
    {
        $this->logger->info(sprintf('New marking store created while setting marking "%s" to store "%s".'), $markingId, $storeId);
    }

    public function onSet(Event $event)
    {
        foreach ($event->getTransition()->getTos() as $place) {
            $this->logger->info(sprintf('Set marking "%s" to store "%s".', $markingId, $storeId));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'backend.mark.setting' => ['onSetting'],
            'backend.mark.newstore' => ['onNewStore'],
            'backend.mark.set' => ['onSet'],
        ];
    }
}
