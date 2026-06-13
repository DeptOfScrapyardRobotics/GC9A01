<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Concerns;

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01DisplayFunctionControl;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01FrameRate;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma1;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma2;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma3;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01Gamma4;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01MADControl;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01PowerControl2;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01PowerControl3;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\DataObjects\GC9A01PowerControl4;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Enums\GC9A01ColorMode;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Enums\GC9A01OpCode;
use Exception;

trait GC9A01API
{
    use GC9A01InternalAPI;

    public function displayOn(): void
    {
        $this->command(GC9A01OpCode::TOGGLE_DISPLAY_ON);
        usleep(20000);
    }

    public function displayOff(): void
    {
        $this->command(GC9A01OpCode::TOGGLE_DISPLAY_OFF);
    }

    public function sleepModeOn(): void
    {
        $this->command(GC9A01OpCode::ENTER_SLEEP_MODE);
        usleep(120000);
    }

    public function sleepModeOff(): void
    {
        $this->command(GC9A01OpCode::EXIT_SLEEP_MODE);
        usleep(120000);
    }

    public function displayNormalMode(): void
    {
        $this->command(GC9A01OpCode::NORMAL_MODE_ON);
    }

    public function displayPartialMode(): void
    {
        $this->command(GC9A01OpCode::PARTIAL_MODE_ON);
    }

    public function displayInversionOn(): void
    {
        $this->command(GC9A01OpCode::DISPLAY_INVERSION_ON);
    }

    public function displayInversionOff(): void
    {
        $this->command(GC9A01OpCode::DISPLAY_INVERSION_OFF);
    }

    public function tearingEffectLineOn(): void
    {
        $this->command(GC9A01OpCode::TEARING_EFFECT_LINE_ON);
    }

    public function tearingEffectLineOff(): void
    {
        $this->command(GC9A01OpCode::TEARING_EFFECT_LINE_OFF);
    }

    /**
     * Revealed secret register: unlock the manufacturer register banks.
     * Both Inter Register Enable commands must be written to expose the
     * extended registers (power control, frame rate, gamma, ...).
     */
    public function interRegisterEnable1(): void
    {
        $this->command(GC9A01OpCode::INTER_REGISTER_ENABLE_1);
    }

    /**
     * Revealed secret register: second half of the manufacturer unlock pair.
     */
    public function interRegisterEnable2(): void
    {
        $this->command(GC9A01OpCode::INTER_REGISTER_ENABLE_2);
    }

    public function setMADControl(GC9A01MADControl $control): void
    {
        $this->command(GC9A01OpCode::MEMORY_ACCESS_CONTROL, [$control->toByte()]);
    }

    public function setDisplayFunctionControl(GC9A01DisplayFunctionControl $control): void
    {
        $this->command(GC9A01OpCode::DISPLAY_FUNCTION_CONTROL, $control->toBytes());
    }

    public function setPowerControl2(GC9A01PowerControl2 $register): void
    {
        $this->command(GC9A01OpCode::POWER_CONTROL_2, [$register->toByte()]);
    }

    public function setPowerControl3(GC9A01PowerControl3 $register): void
    {
        $this->command(GC9A01OpCode::POWER_CONTROL_3, [$register->toByte()]);
    }

    public function setPowerControl4(GC9A01PowerControl4 $register): void
    {
        $this->command(GC9A01OpCode::POWER_CONTROL_4, [$register->toByte()]);
    }

    public function setFrameRate(GC9A01FrameRate $register): void
    {
        $this->command(GC9A01OpCode::FRAME_RATE, [$register->toByte()]);
    }

    public function setGamma1(GC9A01Gamma1 $gamma): void
    {
        $this->command(GC9A01OpCode::SET_GAMMA1, $gamma->toBytes());
    }

    public function setGamma2(GC9A01Gamma2 $gamma): void
    {
        $this->command(GC9A01OpCode::SET_GAMMA2, $gamma->toBytes());
    }

    public function setGamma3(GC9A01Gamma3 $gamma): void
    {
        $this->command(GC9A01OpCode::SET_GAMMA3, $gamma->toBytes());
    }

    public function setGamma4(GC9A01Gamma4 $gamma): void
    {
        $this->command(GC9A01OpCode::SET_GAMMA4, $gamma->toBytes());
    }

    /**
     * @throws Exception
     */
    public function setPixelFormat(GC9A01ColorMode|int $color_mode): void
    {
        if (is_int($color_mode)) {
            $color_mode = match ($color_mode) {
                12 => GC9A01ColorMode::COLOR12,
                16 => GC9A01ColorMode::COLOR16,
                18 => GC9A01ColorMode::COLOR18,
                default => throw new Exception("Invalid color mode: $color_mode")
            };
        }

        $this->command(GC9A01OpCode::SET_PIXEL_FORMAT, [$color_mode->value]);
        $this->color_mode = $color_mode;
    }

    public function setAddressWindow(int $x, int $y, int $width, int $height): void
    {
        $x_end = $x + $width - 1;
        $y_end = $y + $height - 1;
        $this->command(GC9A01OpCode::SET_COLUMN_ADDRESS, [
            ($x >> 8) & 0xFF, $x & 0xFF, ($x_end >> 8) & 0xFF, $x_end & 0xFF,
        ]);
        $this->command(GC9A01OpCode::SET_ROW_ADDRESS, [
            ($y >> 8) & 0xFF, $y & 0xFF, ($y_end >> 8) & 0xFF, $y_end & 0xFF,
        ]);
    }

    public function writeFrame(array $data): void
    {
        $this->command(GC9A01OpCode::WRITE_MEMORY_START);
        $this->data($data);
    }
}
