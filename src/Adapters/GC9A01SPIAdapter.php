<?php

namespace ScrapyardIO\Displays\Color\GC9A01\Adapters;

use ScrapyardIO\Displays\Adapters\ColorDisplayAdapter;
use ScrapyardIO\Displays\Color\GC9A01\Concerns\GC9A01SPIChip;
use ScrapyardIO\Displays\Color\GC9A01\Concerns\GC9A01BootSequence;

class GC9A01SPIAdapter extends ColorDisplayAdapter
{
    use GC9A01SPIChip;
    use GC9A01BootSequence;

    public function bus(int $bus):static
    {
        $this->spi_gc9a01_bus($bus);
        return $this;
    }

    public function chipSelect(int $cs):static
    {
        $this->spi_gc9a01_chip_select($cs);
        return $this;
    }

    public function dcPin(int $chip, int $line): static
    {
        $this->dc_chip($chip);
        $this->dc_line($line);
        $this->dc_gpio();

        return $this;
    }

    public function rstPin(int $chip, int $line): static
    {
        $this->rst_chip($chip);
        $this->rst_line($line);
        $this->rst_gpio();

        return $this;
    }

    public function offsets(int $x, int $y): static
    {
        $this->y_offset = $y;
        $this->x_offset = $x;
        return $this;
    }

    public function boot(): static
    {
        $this->gc9a01_spi();

        $this->max_packet_size = 8192;

        $this->resetSequence();
        $this->mysterySequence1();
        $this->mysterySequence2();
        $this->setMADControl();
        $this->setPixelFormat();
        $this->mysterySequence3();
        $this->setPowerControl();
        $this->mysterySequence4();
        $this->setGamma();
        $this->mysterySequence5();
        $this->setFrameRate();
        $this->mysterySequence6();
        $this->tearingOn();
        $this->inversionOn();
        $this->exitSleepMode();
        $this->turnDisplayOn();

        return $this;
    }
}
