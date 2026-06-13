<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

/**
 * Revealed secret register: SET_GAMMA1 (0xF0, datasheet 6.4.10).
 *
 * 6 parameter bytes describing one of the four gamma correction banks. The
 * defaults are the reference GC9A01 bring-up values.
 */
readonly class GC9A01Gamma1
{
    public function __construct(
        public int $p1 = 0x45,
        public int $p2 = 0x09,
        public int $p3 = 0x08,
        public int $p4 = 0x08,
        public int $p5 = 0x26,
        public int $p6 = 0x2A,
    ) {}

    /**
     * @return list<int> The 6 parameter bytes in datasheet order.
     */
    public function toBytes(): array
    {
        return array_map(
            static fn (int $value): int => $value & 0xFF,
            [
                $this->p1,
                $this->p2,
                $this->p3,
                $this->p4,
                $this->p5,
                $this->p6,
            ]
        );
    }

    /**
     * @param  list<int>  $bytes  The 6 parameter bytes in datasheet order.
     */
    public static function fromBytes(array $bytes): static
    {
        return new static(...array_map(
            static fn (int $value): int => $value & 0xFF,
            array_values($bytes)
        ));
    }
}
