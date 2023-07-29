<?php

namespace Shelter\Automation\Module;

use inisire\mqtt\NetBus\EventBus;
use inisire\mqtt\NetBus\QueryBus;
use Psr\Log\LoggerInterface;
use Shelter\Automation\Core\Scenario;
use Shelter\Automation\Core\Scenario\Configuration;
use Shelter\Core\DeviceRegistry;
use Symfony\Component\Finder\Finder;

class ScenarioFactory
{
    /**
     * @var array<Scenario>
     */
    private array $scenarios = [];

    public function __construct(
        private readonly DeviceRegistry  $registry,
        private readonly QueryBus        $queryBus,
        private readonly EventBus        $eventBus,
        private readonly LoggerInterface $logger,
        private readonly string $scenariosPath
    )
    {
        $finder = new Finder();
        $files = $finder
            ->in($this->scenariosPath)
            ->name('*.php')
            ->files();

        foreach ($files as $file) {
            require_once $file->getPathname();
        }

        foreach (get_declared_classes() as $class) {
            if (is_a($class, Scenario::class, true)) {
                $this->logger->debug(sprintf('Scenario "%s" loaded', $class));
                $this->scenarios[] = $class;
            }
        }
    }

    public function create(Configuration $configuration): Scenario
    {
        foreach ($this->scenarios as $scenario) {
            if ($scenario::getName() === $configuration->getName()) {
                return new $scenario($this->registry, $this->queryBus, $this->eventBus, $this->logger, $configuration);
            }
        }

        throw new \RuntimeException(sprintf('Scanario "%s" is not exists', $configuration->getName()));
    }
}