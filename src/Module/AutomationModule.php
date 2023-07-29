<?php

namespace Shelter\Automation\Module;

use inisire\mqtt\NetBus\EventBus;
use inisire\NetBus\Event\CallableSubscription;
use inisire\NetBus\Event\EventInterface;
use inisire\NetBus\Event\Subscription\Matches;
use inisire\NetBus\Event\Subscription\Wildcard;
use Shelter\Automation\Core\Scenario;
use Shelter\Bus\Event\DiscoverRequest;
use Shelter\Bus\Event\DiscoverResponse;
use Shelter\Bus\Events;


class AutomationModule
{
    /**
     * @var array<Scenario>
     */
    private array $scenarios = [];

    public function __construct(
        private readonly EventBus        $eventBus,
        private readonly Configuration   $configuration,
        private readonly ScenarioFactory $scenarioFactory
    )
    {
        foreach ($this->configuration->getScenarios() as $scenarioConfiguration) {
            $this->scenarios[] = $this->scenarioFactory->create($scenarioConfiguration);
        }
    }

    public function start(): void
    {
        $this->eventBus->subscribe(new CallableSubscription(
            new Wildcard(),
            new Matches([Events::DISCOVER_REQUEST]),
            function (EventInterface $event) {
                $data = $event->getData();
                $this->onDiscovery(new DiscoverRequest($data['source']));
            }
        ));

        foreach ($this->scenarios as $scenario) {
            $scenario->start();
        }
    }

    public function onDiscovery(DiscoverRequest $request): void
    {
        foreach ($this->scenarios as $scenario) {
            $this->eventBus->dispatch($scenario->getId(), new DiscoverResponse($scenario->getId(), $scenario->getName(), $scenario->getProperties()));
        }
    }
}