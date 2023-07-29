<?php

namespace Shelter\Automation\Core;

use Psr\Log\LoggerInterface;
use Shelter\Core\DeviceRegistry;
use inisire\mqtt\NetBus\QueryBus;
use inisire\mqtt\NetBus\EventBus;
use Shelter\Automation\Core\Scenario\Configuration;

abstract class Scenario
{
    public function __construct(
        protected readonly DeviceRegistry  $registry,
        protected readonly QueryBus        $queryBus,
        protected readonly EventBus        $eventBus,
        protected readonly LoggerInterface $logger,
        protected readonly Configuration   $configuration
    )
    {

    }

    abstract public function start(): bool;

    abstract public static function getName(): string;

    public function getId(): string
    {
        return $this->configuration->getId();
    }

    public function getProperties(): array
    {
        return [];
    }
}