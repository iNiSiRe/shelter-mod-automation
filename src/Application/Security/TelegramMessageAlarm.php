<?php

namespace Shelter\Automation\Application\Security;

use Shelter\Automation\Application\Notification\Service\Telegram;
use Shelter\Core\Device\Security\Alarm;
use function inisire\fibers\asleep;
use function inisire\fibers\async;

class TelegramMessageAlarm implements Alarm
{
    private bool $enabled = false;

    public function __construct(
        private readonly Telegram $telegram,
        private readonly string   $telegramChatId,
        private readonly string   $enableMessage,
        private readonly string   $disableMessage
    )
    {
    }

    public function enableAlarm(?float $duration = null): void
    {
        if ($this->isEnabled()) {
            return;
        }

        $this->enabled = true;
        $this->telegram->sendMessage($this->telegramChatId, $this->enableMessage);

        if ($duration) {
            async(function () use ($duration) {
                asleep($duration);
                $this->disableAlarm();
            });
        }
    }

    public function disableAlarm(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->enabled = false;
        $this->telegram->sendMessage($this->telegramChatId, $this->disableMessage);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}