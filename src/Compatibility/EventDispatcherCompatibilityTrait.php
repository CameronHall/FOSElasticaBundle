<?php

namespace FOS\ElasticaBundle\Compatibility;

use Symfony\Component\EventDispatcher\EventDispatcherInterface as LegacyEventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

trait EventDispatcherCompatibilityTrait
{
    /**
     * @var EventDispatcherInterface|LegacyEventDispatcherInterface
     */
    private $dispatcher;

    private function dispatch($event): void
    {
        $eventName = get_class($event);
        if ($this->dispatcher instanceof EventDispatcherInterface) {
            // Symfony >= 4.3
            $this->dispatcher->dispatch($event, $eventName);
        } else {
            // Symfony 3.4
            $this->dispatcher->dispatch($eventName, $event);
        }
    }
}