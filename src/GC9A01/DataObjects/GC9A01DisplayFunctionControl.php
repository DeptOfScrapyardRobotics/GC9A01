<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects;

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Exceptions\GC9A01Exception;

/**
 * DFC (0xB6) — display function control.
 *
 * 2 parameter bytes controlling the scan/source gate direction and the gate
 * scan interval. The defaults (0x00, 0x20) are the reference GC9A01 settings
 * for a 240x240 round panel.
 */
readonly class GC9A01DisplayFunctionControl
{
    public function __construct(
        public int $scan_control = 0x00,
        public int $gate_scan = 0x20,
    ) {
        $this->assertByte($this->scan_control, 'scan_control');
        $this->assertByte($this->gate_scan, 'gate_scan');
    }

    private function assertByte(int $value, string $field): void
    {
        if (($value < 0) || ($value > 0xFF)) {
            throw GC9A01Exception::invalidRegisterValue($field, $value, 0, 0xFF);
        }
    }

    /**
     * @return list<int>
     */
    public function toBytes(): array
    {
        return [
            $this->scan_control & 0xFF,
            $this->gate_scan & 0xFF,
        ];
    }

    public static function fromBytes(
        int $scan_control = 0x00,
        int $gate_scan = 0x20,
    ): static {
        return new static($scan_control, $gate_scan);
    }
}
