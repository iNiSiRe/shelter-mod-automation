<?php

namespace Shelter\Automation\Core\Security;

use Shelter\Automation\Application\Notification\Contract\Messenger;
use Shelter\Core\Device\Security\Alarm;
use Shelter\Core\Device\Security\Guard;
use Shelter\Core\Device\Sensor\Contract\MagnetSensor;
use Shelter\Core\Device\Sensor\Contract\MotionSensor;

class IllegalEntryGuard
{
    /**
     * @param array<MagnetSensor> $doorSensors
     * @param array<MotionSensor> $motionSensors
     */
    public function __construct(
        private readonly Guard     $guard,
        private readonly ?Alarm    $alarm,
        private readonly Messenger $messenger,
        private readonly array     $doorSensors,
        private readonly array     $motionSensors
    )
    {
        foreach ($this->doorSensors as $sensor) {
            $sensor->onOpenUpdate(function (bool $open) use ($sensor) {
                if ($open === true) {
                    $this->onOpen($sensor);
                } else {
                    $this->onClose($sensor);
                }
            });
        }

        foreach ($this->motionSensors as $sensor) {
            $sensor->onMotion(function () use ($sensor) {
                $this->onMotion($sensor);
            });
        }
    }

    public function onOpen(MagnetSensor $sensor): void
    {
        if ($sensor->isOpen() === true && $this->guard->isGuardActive()) {
            $this->messenger->sendMessage(sprintf('Magnet sensor "%s" is opened', $sensor->getId()));
            $this->alarm?->enableAlarm(10.0);
        }
    }

    public function onClose(MagnetSensor $sensor): void
    {
        if ($sensor->isOpen() === false && $this->guard->isGuardActive()) {
            $this->messenger->sendMessage(sprintf('Magnet sensor "%s" is closed', $sensor->getId()));
        }
    }

    public function onMotion(MotionSensor $sensor): void
    {
        if ($sensor->isActive() && $this->guard->isGuardActive()) {
            $this->messenger->sendMessage(sprintf('Motion detected on sensor "%s"', $sensor->getId()));
            $this->alarm?->enableAlarm(10.0);
        }
    }
}