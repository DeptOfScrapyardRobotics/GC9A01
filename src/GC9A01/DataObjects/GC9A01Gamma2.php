<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

/**
 * Revealed secret register: SET_GAMMA2 (0xF1, datasheet 6.4.11).
 * Identical structure to SET_GAMMA1; only the default tuning values differ.
 */
readonly class GC9A01Gamma2 extends GC9A01Gamma1
{
    public function __construct(
        int $p1 = 0x43,
        int $p2 = 0x70,
        int $p3 = 0x72,
        int $p4 = 0x36,
        int $p5 = 0x37,
        int $p6 = 0x6F,
    ) {
        parent::__construct($p1, $p2, $p3, $p4, $p5, $p6);
    }
}
