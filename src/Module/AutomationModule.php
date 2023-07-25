<?php

namespace Shelter\Automation\Module;

use inisire\NetBus\Event\EventBusInterface;
use inisire\NetBus\Query\QueryBusInterface;

class AutomationModule
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly EventBusInterface $eventBus
    )
    {

    }
}