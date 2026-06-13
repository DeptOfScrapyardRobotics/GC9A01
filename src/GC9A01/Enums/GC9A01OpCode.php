<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Enums;

/**
 * GC9A01 command set.
 *
 * The GC9A01 is a "regular" MIPI DCS controller wrapped around a thick layer of
 * manufacturer magic. Three flavours of register live here:
 *
 *   1. Standard MIPI DCS commands documented in the datasheet (sleep, display
 *      on/off, address windows, MADCTL, COLMOD, ...).
 *   2. Revealed secret registers — manufacturer (Level 3) commands that ARE
 *      documented in the GC9A01 datasheet but are absent from the generic MIPI
 *      DCS set: the inter-register unlock pair, power control, frame rate and
 *      the four gamma banks. We know what these do, so they get real names.
 *   3. MYSTERY_REGISTER_xx — the undocumented init-sequence registers nobody at
 *      the foundry felt like writing down. They have fixed, copy-the-magic
 *      payloads (see {@see GC9A01OpCode::mysteryPayload()}) and are numbered in
 *      the order they are first encountered while booting the panel.
 */
enum GC9A01OpCode: int
{
    // ----------------------------------------------------------------------
    // Standard MIPI DCS commands (datasheet section 6.2 / 6.3)
    // ----------------------------------------------------------------------
    case SOFTWARE_RESET = 0x01;
    case ENTER_SLEEP_MODE = 0x10;
    case EXIT_SLEEP_MODE = 0x11;
    case PARTIAL_MODE_ON = 0x12;
    case NORMAL_MODE_ON = 0x13;

    case DISPLAY_INVERSION_OFF = 0x20;
    case DISPLAY_INVERSION_ON = 0x21;

    case TOGGLE_DISPLAY_OFF = 0x28;
    case TOGGLE_DISPLAY_ON = 0x29;
    case SET_COLUMN_ADDRESS = 0x2A;
    case SET_ROW_ADDRESS = 0x2B;
    case WRITE_MEMORY_START = 0x2C;

    case TEARING_EFFECT_LINE_OFF = 0x34;
    case TEARING_EFFECT_LINE_ON = 0x35;                 // + optional 1 byte (TE mode)
    case MEMORY_ACCESS_CONTROL = 0x36;                  // MADCTL (+ 1 byte)
    case SET_PIXEL_FORMAT = 0x3A;                       // COLMOD (+ 1 byte)

    case DISPLAY_FUNCTION_CONTROL = 0xB6;               // + 2 bytes

    // ----------------------------------------------------------------------
    // Revealed secret registers — manufacturer Level 3 commands (section 6.4).
    // Documented in the GC9A01 datasheet only; not part of the generic MIPI set.
    // ----------------------------------------------------------------------

    /** Revealed secret register: Inter Register Enable 1 (datasheet 6.4.8). Unlocks bank access. */
    case INTER_REGISTER_ENABLE_1 = 0xFE;

    /** Revealed secret register: Inter Register Enable 2 (datasheet 6.4.9). Unlocks bank access. */
    case INTER_REGISTER_ENABLE_2 = 0xEF;

    /** Revealed secret register: Power Control 2 / VREG1A voltage (datasheet 6.4.4). */
    case POWER_CONTROL_2 = 0xC3;

    /** Revealed secret register: Power Control 3 / VREG1B voltage (datasheet 6.4.5). */
    case POWER_CONTROL_3 = 0xC4;

    /** Revealed secret register: Power Control 4 / VREG2A voltage (datasheet 6.4.6). */
    case POWER_CONTROL_4 = 0xC9;

    /** Revealed secret register: Frame Rate (datasheet 6.4.1). */
    case FRAME_RATE = 0xE8;

    /** Revealed secret register: SET_GAMMA1 (datasheet 6.4.10). */
    case SET_GAMMA1 = 0xF0;

    /** Revealed secret register: SET_GAMMA2 (datasheet 6.4.11). */
    case SET_GAMMA2 = 0xF1;

    /** Revealed secret register: SET_GAMMA3 (datasheet 6.4.12). */
    case SET_GAMMA3 = 0xF2;

    /** Revealed secret register: SET_GAMMA4 (datasheet 6.4.13). */
    case SET_GAMMA4 = 0xF3;

    // ----------------------------------------------------------------------
    // Undocumented init-sequence registers. Numbered in boot-encounter order.
    // Their payloads are fixed manufacturer magic — see mysteryPayload().
    // ----------------------------------------------------------------------
    case MYSTERY_REGISTER_1 = 0xEB;
    case MYSTERY_REGISTER_2 = 0x84;
    case MYSTERY_REGISTER_3 = 0x85;
    case MYSTERY_REGISTER_4 = 0x86;
    case MYSTERY_REGISTER_5 = 0x87;
    case MYSTERY_REGISTER_6 = 0x88;
    case MYSTERY_REGISTER_7 = 0x89;
    case MYSTERY_REGISTER_8 = 0x8A;
    case MYSTERY_REGISTER_9 = 0x8B;
    case MYSTERY_REGISTER_10 = 0x8C;
    case MYSTERY_REGISTER_11 = 0x8D;
    case MYSTERY_REGISTER_12 = 0x8E;
    case MYSTERY_REGISTER_13 = 0x8F;
    case MYSTERY_REGISTER_14 = 0x90;
    case MYSTERY_REGISTER_15 = 0xBD;
    case MYSTERY_REGISTER_16 = 0xBC;
    case MYSTERY_REGISTER_17 = 0xFF;
    case MYSTERY_REGISTER_18 = 0xBE;
    case MYSTERY_REGISTER_19 = 0xE1;
    case MYSTERY_REGISTER_20 = 0xDF;
    case MYSTERY_REGISTER_21 = 0xED;
    case MYSTERY_REGISTER_22 = 0xAE;
    case MYSTERY_REGISTER_23 = 0xCD;
    case MYSTERY_REGISTER_24 = 0x70;
    case MYSTERY_REGISTER_25 = 0x62;
    case MYSTERY_REGISTER_26 = 0x63;
    case MYSTERY_REGISTER_27 = 0x64;
    case MYSTERY_REGISTER_28 = 0x66;
    case MYSTERY_REGISTER_29 = 0x67;
    case MYSTERY_REGISTER_30 = 0x74;
    case MYSTERY_REGISTER_31 = 0x98;

    /**
     * Fixed manufacturer payload for an undocumented init register.
     *
     * These bytes are reproduced verbatim from the reference panel bring-up
     * sequence. We have no idea what most of them mean; the silicon does.
     *
     * @return list<int> The data bytes to clock out after the opcode, or an
     *                   empty list for documented / non-mystery commands.
     */
    public function mysteryPayload(): array
    {
        return match ($this) {
            self::MYSTERY_REGISTER_1 => [0x14],
            self::MYSTERY_REGISTER_2 => [0x40],
            self::MYSTERY_REGISTER_3 => [0xFF],
            self::MYSTERY_REGISTER_4 => [0xFF],
            self::MYSTERY_REGISTER_5 => [0xFF],
            self::MYSTERY_REGISTER_6 => [0x0A],
            self::MYSTERY_REGISTER_7 => [0x21],
            self::MYSTERY_REGISTER_8 => [0x00],
            self::MYSTERY_REGISTER_9 => [0x80],
            self::MYSTERY_REGISTER_10 => [0x01],
            self::MYSTERY_REGISTER_11 => [0x01],
            self::MYSTERY_REGISTER_12 => [0xFF],
            self::MYSTERY_REGISTER_13 => [0xFF],
            self::MYSTERY_REGISTER_14 => [0x08, 0x08, 0x08, 0x08],
            self::MYSTERY_REGISTER_15 => [0x06],
            self::MYSTERY_REGISTER_16 => [0x00],
            self::MYSTERY_REGISTER_17 => [0x60, 0x01, 0x04],
            self::MYSTERY_REGISTER_18 => [0x11],
            self::MYSTERY_REGISTER_19 => [0x10, 0x0E],
            self::MYSTERY_REGISTER_20 => [0x21, 0x0C, 0x02],
            self::MYSTERY_REGISTER_21 => [0x1B, 0x0B],
            self::MYSTERY_REGISTER_22 => [0x77],
            self::MYSTERY_REGISTER_23 => [0x63],
            self::MYSTERY_REGISTER_24 => [0x07, 0x07, 0x04, 0x0E, 0x0F, 0x09, 0x07, 0x08, 0x03],
            self::MYSTERY_REGISTER_25 => [0x18, 0x0D, 0x71, 0xED, 0x70, 0x70, 0x18, 0x0F, 0x71, 0xEF, 0x70, 0x70],
            self::MYSTERY_REGISTER_26 => [0x18, 0x11, 0x71, 0xF1, 0x70, 0x70, 0x18, 0x13, 0x71, 0xF3, 0x70, 0x70],
            self::MYSTERY_REGISTER_27 => [0x28, 0x29, 0xF1, 0x01, 0xF1, 0x00, 0x07],
            self::MYSTERY_REGISTER_28 => [0x3C, 0x00, 0xCD, 0x67, 0x45, 0x45, 0x10, 0x00, 0x00, 0x00],
            self::MYSTERY_REGISTER_29 => [0x00, 0x3C, 0x00, 0x00, 0x00, 0x01, 0x54, 0x10, 0x32, 0x98],
            self::MYSTERY_REGISTER_30 => [0x10, 0x85, 0x80, 0x00, 0x00, 0x4E, 0x00],
            self::MYSTERY_REGISTER_31 => [0x3E, 0x07],
            default => [],
        };
    }
}
