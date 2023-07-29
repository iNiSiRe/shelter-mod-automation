<?php

namespace Shelter\Automation\Module;

use Symfony\Component\Yaml\Yaml;

class Configuration
{
    public function __construct(
        private readonly array $parameters
    )
    {
    }

    /**
     * @return iterable<\Shelter\Automation\Core\Scenario\Configuration>
     */
    public function getScenarios(): iterable
    {
        $configurations = $this->parameters['scenario'] ?? [];

        foreach ($configurations as $id => $configuration) {
            yield new \Shelter\Automation\Core\Scenario\Configuration($id, $configuration);
        }
    }

    public static function fromYaml(string $path): static
    {
        return new self(Yaml::parse(file_get_contents($path)));
    }
}