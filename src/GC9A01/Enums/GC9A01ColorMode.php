<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Enums;

/**
 * Interface Pixel Format (COLMOD, 0x3A) register values.
 *
 * Bits [2:0] select the MCU (control) interface colour depth used over SPI.
 * The canonical GC9A01 bring-up clocks out 0x05 for RGB565.
 */
enum GC9A01ColorMode: int
{
    /**
     * 12-bit per pixel (4096 colors)
     * RGB444 format
     */
    case COLOR12 = 0x03;

    /**
     * 16-bit per pixel (65K colors)
     * RGB565 format (5 bits red, 6 bits green, 5 bits blue)
     */
    case COLOR16 = 0x05;

    /**
     * 18-bit per pixel (262K colors)
     * RGB666 format (6 bits per color)
     */
    case COLOR18 = 0x06;

    public function bitsPerPixel(): int
    {
        return match ($this) {
            self::COLOR12 => 12,
            self::COLOR16 => 16,
            self::COLOR18 => 18,
        };
    }
}
