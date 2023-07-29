<?php

namespace Shelter\Automation\Core\Scenario;

class Configuration
{
    public function __construct(
        private readonly string $id,
        private readonly array  $config
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }


    public function getName(): string
    {
        var_dump($this->config);

        return $this->config['name'];
    }

    public function get(string $name): mixed
    {
        $value = $this->config['parameters'][$name] ?? null;

        if ($value === null) {
            throw new \RuntimeException(sprintf('Scenario parameter "%s" not exists', $name));
        }

        return $value;
    }
}