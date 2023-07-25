<?php

namespace Shelter\Automation\Core\Security;

class Guard implements \Shelter\Core\Device\Security\Guard
{
    private bool $enabled = false;

    public function isGuardActive(): bool
    {
        return $this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }
}