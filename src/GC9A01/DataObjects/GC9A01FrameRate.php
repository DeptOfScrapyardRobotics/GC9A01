<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

use BareMetal\DataObjects\DataRegister;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Exceptions\GC9A01Exception;

/**
 * Revealed secret register: Frame Rate (0xE8, datasheet 6.4.1).
 *
 * Single parameter byte selecting the panel refresh rate divider. The default
 * 0x34 is the reference setting.
 */
readonly class GC9A01FrameRate extends DataRegister
{
    public function __construct(
        public int $rate = 0x34,
    ) {
        if (($this->rate < 0) || ($this->rate > 0xFF)) {
            throw GC9A01Exception::invalidRegisterValue('rate', $this->rate, 0, 0xFF);
        }
    }

    public function toBits(): string
    {
        return str_pad(decbin($this->rate & 0xFF), 8, '0', STR_PAD_LEFT);
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
