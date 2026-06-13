<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

use BareMetal\DataObjects\DataRegister;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Exceptions\GC9A01Exception;

/**
 * Revealed secret register: Power Control 2 (0xC3, datasheet 6.4.4).
 *
 * Single parameter byte selecting the VREG1A operating voltage that feeds the
 * positive grayscale reference. The default 0x13 is the reference setting.
 */
readonly class GC9A01PowerControl2 extends DataRegister
{
    public function __construct(
        public int $vreg1a = 0x13,
    ) {
        if (($this->vreg1a < 0) || ($this->vreg1a > 0xFF)) {
            throw GC9A01Exception::invalidRegisterValue('vreg1a', $this->vreg1a, 0, 0xFF);
        }
    }

    public function toBits(): string
    {
        return str_pad(decbin($this->vreg1a & 0xFF), 8, '0', STR_PAD_LEFT);
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
