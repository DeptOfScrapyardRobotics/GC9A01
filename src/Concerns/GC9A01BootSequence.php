<?php

namespace ScrapyardIO\Displays\Color\GC9A01\Concerns;

use ScrapyardIO\Displays\Colors\Color;
use ScrapyardIO\Support\DataManipulation\ByteRegister;
use ScrapyardIO\Displays\Color\GC9A01\Enums\GC9A01Command;

trait GC9A01BootSequence
{
    protected array $gamma = [
        [0x45, 0x09, 0x08, 0x08, 0x26, 0x2A],
        [0x43, 0x70, 0x72, 0x36, 0x37, 0x6F],
        [0x45, 0x09, 0x08, 0x08, 0x26, 0x2A],
        [0x43, 0x70, 0x72, 0x36, 0x37, 0x6F]
    ];

    protected int $x_offset = 0;
    protected int $y_offset = 0;
    protected bool $bgr_order_mode = true;              //RGB
    protected bool $right_left_refresh = false;         //MH
    protected bool $bottom_top_refresh = false;         //ML
    protected bool $bottom_top_row_addresses = false;   //MY
    protected bool $pixel_direction_vertical = false;   //MV
    protected bool $right_left_column_addresses = true; //MX

    abstract public function wait(int $ms): void;
    abstract public function sendCommand(array $bytes): void;

    protected function mysterySequence1(): void
    {
        $this->sendCommand([GC9A01Command::INTER_REGISTER_ENABLE2->value]);
        $this->sendCommand([GC9A01Command::SECRET_BOOT_REGISTER->value, 0x14]);
        $this->sendCommand([GC9A01Command::INTER_REGISTER_ENABLE1->value]);
        $this->sendCommand([GC9A01Command::INTER_REGISTER_ENABLE2->value]);
        $this->sendCommand([GC9A01Command::SECRET_BOOT_REGISTER->value, 0x14]);
    }

    protected function mysterySequence2(): void
    {
        $this->sendCommand([GC9A01Command::SECRET_SETTING1->value, 0x40]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING2->value, 0xFF]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING3->value, 0xFF]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING4->value, 0xFF]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING5->value, 0x0A]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING6->value, 0x21]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING7->value, 0x00]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING8->value, 0x01]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING9->value, 0x01]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING10->value, 0x01]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING11->value, 0xFF]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING12->value, 0xFF]);

        $this->sendCommand([
            GC9A01Command::DISPLAY_FUNCTION_CONTROL->value,
            0x00, 0x00
        ]);
    }

    protected function mysterySequence3(): void
    {
        $this->sendCommand([
            GC9A01Command::SECRET_SETTING13->value,
            ...[0x08, 0x08, 0x08, 0x08]
        ]);

        $this->sendCommand([GC9A01Command::SECRET_SETTING14->value, 0x06]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING15->value, 0x00]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING16->value,
            ...[0x60, 0x01, 0x04]
        ]);
    }

    protected function mysterySequence4(): void
    {
        $this->sendCommand([GC9A01Command::SECRET_SETTING17->value, 0x11]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING18->value,
            ...[0x10, 0x0E]
        ]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING19->value,
            ...[0x21, 0x0c, 0x02]
        ]);
    }

    protected function mysterySequence5(): void
    {
        $this->sendCommand([
            GC9A01Command::SECRET_SETTING20->value,
            ...[0x1B, 0x0B]
        ]);

        $this->sendCommand([GC9A01Command::SECRET_SETTING21->value, 0x77]);
        $this->sendCommand([GC9A01Command::SECRET_SETTING22->value, 0x63]);

        // Unsure what this line (from manufacturer's boilerplate code) is
        // meant to do, but users reported issues, seems to work OK without:
        //$this->sendCommand(
        //    GC9A01Command::SECRET_SETTING23->value,
        //    [0x07, 0x07, 0x04, 0x0E, 0x0F, 0x09, 0x07, 0x08, 0x03]
        //);
    }

    protected function mysterySequence6(): void
    {
        $this->sendCommand([
            GC9A01Command::SECRET_SETTING24->value,
            ...[0x18, 0x0D, 0x71, 0xED, 0x70, 0x70,0x18, 0x0F, 0x71, 0xEF, 0x70, 0x70]
        ]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING25->value,
            ...[0x18, 0x11, 0x71, 0xF1, 0x70, 0x70,0x18, 0x13, 0x71, 0xF3, 0x70, 0x70]
        ]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING26->value,
            ...[0x28, 0x29, 0xF1, 0x01, 0xF1, 0x00, 0x07]
        ]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING27->value,
            ...[0x3C, 0x00, 0xCD, 0x67, 0x45, 0x45, 0x10, 0x00, 0x00, 0x00]
        ]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING28->value,
            ...[0x00, 0x3C, 0x00, 0x00, 0x00, 0x01, 0x54, 0x10, 0x32, 0x98]
        ]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING29->value,
            ...[0x10, 0x85, 0x80, 0x00, 0x00, 0x4E, 0x00]
        ]);

        $this->sendCommand([
            GC9A01Command::SECRET_SETTING30->value,
            ...[0x3e, 0x07]
        ]);
    }

    protected function setMADControl(): void
    {
        $this->sendCommand([
            GC9A01Command::MEMORY_ACCESS_CONTROL->value,
            ...[$this->memoryAccessControl()]
        ]);
    }

    protected function setPixelFormat(): void
    {
        $this->sendCommand([GC9A01Command::PIXEL_FORMAT_SET->value, 0x05]);
    }

    protected function setPowerControl(): void
    {
        $this->sendCommand([GC9A01Command::POWER_CONTROL_2->value, 0x13]);
        $this->sendCommand([GC9A01Command::POWER_CONTROL_3->value, 0x13]);
        $this->sendCommand([GC9A01Command::POWER_CONTROL_4->value, 0x22]);
    }

    protected function setGamma(): void
    {
        $this->sendCommand([GC9A01Command::SET_GAMMA1->value, $this->gamma[0]]);
        $this->sendCommand([GC9A01Command::SET_GAMMA2->value, $this->gamma[1]]);
        $this->sendCommand([GC9A01Command::SET_GAMMA3->value, $this->gamma[2]]);
        $this->sendCommand([GC9A01Command::SET_GAMMA4->value, $this->gamma[3]]);
    }

    protected function setFrameRate(): void
    {
        $this->sendCommand([GC9A01Command::FRAME_RATE->value, 0x34]);
    }

    protected function tearingOn(): void
    {
        $this->sendCommand([GC9A01Command::TEARING_EFFECT_LINE_ON->value]);
    }

    protected function inversionOn(): void
    {
        $this->sendCommand([GC9A01Command::DISPLAY_INVERSION_ON->value]);
    }

    protected function exitSleepMode(): void
    {
        $this->sendCommand([GC9A01Command::EXIT_SLEEP_MODE->value]);
        $this->wait(150);
    }

    protected function turnDisplayOn(): void
    {
        $this->sendCommand([GC9A01Command::DISPLAY_ON->value]);
        $this->wait(150);
    }

    public function memoryAccessControl(): int
    {
        return (new ByteRegister(0))
            ->update(7, $this->bottom_top_row_addresses)
            ->update(6, $this->right_left_column_addresses)
            ->update(5, $this->pixel_direction_vertical)
            ->update(4, $this->bottom_top_refresh)
            ->update(3, $this->bgr_order_mode)
            ->update(2, $this->right_left_refresh)
            ->update(1, 0)
            ->update(0, 0)
            ->byte;
    }

    protected function startWrite(): void
    {
        $this->sendCommand([GC9A01Command::MEMORY_WRITE->value]);
    }

    public function display(): static
    {
        $this->setAddressWindow($this->min_y, $this->max_y, $this->min_x, $this->max_x);

        $colors = array_map(fn(Color $color) => $color->to16BitInt(), $this->wire->toRows());
        $min_bytes = 2;
        $payload = [];
        $idx = 0;
        foreach($colors as $color)
        {
            for ($i = $min_bytes - 1; $i >= 0; $i--) {
                $payload[$idx++] = ($color >> ($i * 8)) & 0xFF;
            }
        }

        $this->startWrite();
        foreach(array_chunk($payload, $this->max_packet_size) as $chunk)
        {
            $this->sendData($chunk);
        }

        return $this;
    }

    protected function setAddressWindow(int $y_min, int $y_max, int $x_min, int $x_max): void
    {
        $this->setXRange($x_min, $x_max);
        $this->setYRange($y_min, $y_max);
    }

    public function setYRange(int $min, int $max): void
    {
        $y_start = $min + $this->y_offset;
        $y_end   = $max + $this->y_offset;

        $this->sendCommand([
            GC9A01Command::ROW_ADDRESS_SET->value,
            ($y_start >> 8) & 0xFF, $y_start & 0xFF,  // Start row (high, low)
            ($y_end >> 8) & 0xFF, $y_end & 0xFF       // End row (high, low)
        ]);
    }

    public function setXRange(int $min, int $max): void
    {
        $x_start = $min + $this->x_offset;
        $x_end   = $max + $this->x_offset;

        $this->sendCommand([
            GC9A01Command::COLUMN_ADDRESS_SET->value,
            ($x_start >> 8) & 0xFF, $x_start & 0xFF,  // Start column (high, low)
            ($x_end >> 8) & 0xFF, $x_end & 0xFF       // End column (high, low)
        ]);
    }
}
