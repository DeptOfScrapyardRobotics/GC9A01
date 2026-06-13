<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

use BareMetal\DataObjects\DataRegister;

/**
 * MADCTL (0x36) — memory data access control, single parameter byte.
 *   bit 7 MY  — row address order (bottom-to-top)
 *   bit 6 MX  — column address order (right-to-left)
 *   bit 5 MV  — row/column exchange (vertical pixel direction)
 *   bit 4 ML  — vertical refresh order (bottom-to-top)
 *   bit 3 RGB — colour order (1 == BGR)
 *   bit 2 MH  — horizontal refresh order (right-to-left)
 *
 * The default 0x08 (BGR only) matches the reference GC9A01 bring-up.
 */
readonly class GC9A01MADControl extends DataRegister
{
    public function __construct(
        public bool $bottom_top_row_addresses = false,
        public bool $right_left_column_addresses = false,
        public bool $pixel_direction_vertical = false,
        public bool $bottom_top_refresh = false,
        public bool $bgr_order_mode = true,
        public bool $right_left_refresh = false,
    ) {}

    public function toBits(): string
    {
        $bit7 = $this->bottom_top_row_addresses ? '1' : '0';
        $bit6 = $this->right_left_column_addresses ? '1' : '0';
        $bit5 = $this->pixel_direction_vertical ? '1' : '0';
        $bit4 = $this->bottom_top_refresh ? '1' : '0';
        $bit3 = $this->bgr_order_mode ? '1' : '0';
        $bit2 = $this->right_left_refresh ? '1' : '0';
        $bits10 = '00';

        return "{$bit7}{$bit6}{$bit5}{$bit4}{$bit3}{$bit2}{$bits10}";
    }

    public static function fromByte(int $byte): static
    {
        $bits = byte2bits($byte);

        return new static(
            (bool) $bits[7],
            (bool) $bits[6],
            (bool) $bits[5],
            (bool) $bits[4],
            (bool) $bits[3],
            (bool) $bits[2],
        );
    }

    public static function none(): static
    {
        return new static(false, false, false, false, false, false);
    }
}
