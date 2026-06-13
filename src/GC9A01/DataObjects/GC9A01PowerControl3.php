<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

use BareMetal\DataObjects\DataRegister;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Exceptions\GC9A01Exception;

/**
 * Revealed secret register: Power Control 3 (0xC4, datasheet 6.4.5).
 *
 * Single parameter byte selecting the VREG1B operating voltage that feeds the
 * negative grayscale reference. The default 0x13 is the reference setting.
 */
readonly class GC9A01PowerControl3 extends DataRegister
{
    public function __construct(
        public int $vreg1b = 0x13,
    ) {
        if (($this->vreg1b < 0) || ($this->vreg1b > 0xFF)) {
            throw GC9A01Exception::invalidRegisterValue('vreg1b', $this->vreg1b, 0, 0xFF);
        }
    }

    public function toBits(): string
    {
        return str_pad(decbin($this->vreg1b & 0xFF), 8, '0', STR_PAD_LEFT);
    }

    public static function fromByte(int $byte): static
    {
        return new static($byte & 0xFF);
    }

    public static function none(): static
    {
        return new static(0);
    }
}
