<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Adapters;

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Enums\GC9A01OpCode;
use Waveforms\Carriers\GPIO\GPIOBus;
use Waveforms\Carriers\SPI\SPIDevice;

class GC9A01SPIDevice extends GC9A01DataCarrier
{
    public function __construct(
        SPIDevice $carrier,
        protected GPIOBus $gpio,
        protected int $max_packet_size
    ) {
        parent::__construct($carrier);
    }

    public function reset(): void
    {
        $this->gpio->rst()->high();
        usleep(3000);

        $this->gpio->rst()->low();
        usleep(3000);

        $this->gpio->rst()->high();
        usleep(3000);
    }

    public function data(array $data): void
    {
        foreach (array_chunk($data, $this->max_packet_size) as $chunk) {
            $this->gpio->dc()->high();
            $this->carrier->write($chunk);
        }
    }

    public function command(GC9A01OpCode $register_hex, array $command_data = []): void
    {
        $this->gpio->dc()->low();
        $this->carrier->write([$register_hex->value]);
        if (count($command_data) > 0) {
            $this->data($command_data);
        }
    }
}
