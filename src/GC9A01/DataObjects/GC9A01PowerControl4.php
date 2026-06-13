<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

use BareMetal\DataObjects\DataRegister;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Exceptions\GC9A01Exception;

/**
 * Revealed secret register: Power Control 4 (0xC9, datasheet 6.4.6).
 *
 * Single parameter byte selecting the VREG2A operating voltage. The default
 * 0x22 is the reference setting.
 */
readonly class GC9A01PowerControl4 extends DataRegister
{
    public function __construct(
        public int $vreg2a = 0x22,
    ) {
        if (($this->vreg2a < 0) || ($this->vreg2a > 0xFF)) {
            throw GC9A01Exception::invalidRegisterValue('vreg2a', $this->vreg2a, 0, 0xFF);
        }
    }

    public function toBits(): string
    {
        return str_pad(decbin($this->vreg2a & 0xFF), 8, '0', STR_PAD_LEFT);
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
