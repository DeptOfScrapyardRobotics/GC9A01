<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

/**
 * Revealed secret register: SET_GAMMA3 (0xF2, datasheet 6.4.12).
 * Identical structure to SET_GAMMA1; only the default tuning values differ.
 */
readonly class GC9A01Gamma3 extends GC9A01Gamma1
{
    public function __construct(
        int $p1 = 0x45,
        int $p2 = 0x09,
        int $p3 = 0x08,
        int $p4 = 0x08,
        int $p5 = 0x26,
        int $p6 = 0x2A,
    ) {
        parent::__construct($p1, $p2, $p3, $p4, $p5, $p6);
    }
}
