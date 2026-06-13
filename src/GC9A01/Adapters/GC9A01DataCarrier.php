<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Adapters;

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Enums\GC9A01OpCode;
use Waveforms\Carriers\SPI\SPIDevice;

abstract class GC9A01DataCarrier
{
    public function __construct(
        protected SPIDevice $carrier
    ) {}

    abstract public function data(array $data): void;

    abstract public function command(GC9A01OpCode $register_hex, array $command_data = []): void;

    public function reset(): void {}
}
