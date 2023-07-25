<?php

namespace Shelter\Automation\Application\Notification\Service;

use Shelter\Automation\Application\Notification\Contract\Messenger;

class TelegramMessenger implements Messenger
{
    public function __construct(
        private readonly Telegram $telegram,
        private readonly string   $telegramChatId,
    )
    {
    }

    public function sendMessage(string $message): void
    {
        $this->telegram->sendMessage($this->telegramChatId, $message);
    }
}