<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Concerns;

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma1;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma2;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma3;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma4;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01PowerControl2;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01PowerControl3;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01PowerControl4;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Enums\GC9A01OpCode;

trait GC9A01InternalAPI
{
    protected function setDisplay(bool $on): void
    {
        $on ? $this->displayOn() : $this->displayOff();
    }

    protected function setSleepMode(bool $on): void
    {
        $on ? $this->sleepModeOn() : $this->sleepModeOff();
    }

    protected function setDisplayInversion(bool $on): void
    {
        $on ? $this->displayInversionOn() : $this->displayInversionOff();
    }

    protected function setNormalDisplayMode(bool $on): void
    {
        $on ? $this->displayNormalMode() : $this->displayPartialMode();
    }

    protected function setTearingEffect(bool $on): void
    {
        $on ? $this->tearingEffectLineOn() : $this->tearingEffectLineOff();
    }

    protected function setPowerControl(
        GC9A01PowerControl2 $pwr_ctrl2,
        GC9A01PowerControl3 $pwr_ctrl3,
        GC9A01PowerControl4 $pwr_ctrl4,
    ): void {
        $this->setPowerControl2($pwr_ctrl2);
        $this->setPowerControl3($pwr_ctrl3);
        $this->setPowerControl4($pwr_ctrl4);
    }

    protected function setColorControl(
        GC9A01Gamma1 $gamma1,
        GC9A01Gamma2 $gamma2,
        GC9A01Gamma3 $gamma3,
        GC9A01Gamma4 $gamma4,
    ): void {
        $this->setGamma1($gamma1);
        $this->setGamma2($gamma2);
        $this->setGamma3($gamma3);
        $this->setGamma4($gamma4);
    }

    /**
     * Clock out an undocumented init register with its fixed manufacturer
     * payload. Nobody knows what these do; the silicon insists on them.
     */
    protected function mystery(GC9A01OpCode $register): void
    {
        $this->command($register, $register->mysteryPayload());
    }

    protected function command(GC9A01OpCode $register_hex, array $command_data = []): void
    {
        $this->carrier->command($register_hex, $command_data);
    }

    protected function data(array $data): void
    {
        $this->carrier->data($data);
    }
}
