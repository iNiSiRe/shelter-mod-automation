<?php

namespace Shelter\Automation\Application\Notification\Contract;

interface Messenger
{
    public function sendMessage(string $message): void;
}