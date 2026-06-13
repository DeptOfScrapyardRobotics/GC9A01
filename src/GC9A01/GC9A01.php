<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01;

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Adapters\GC9A01DataCarrier;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Concerns\GC9A01API;
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
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Exceptions\GC9A01Exception;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Factory\GC9A01Factory;
use Exception;
use RealityInterface\Displays\Attributes\OutputsColor;
use RealityInterface\Displays\Contracts\Applied\FullColorTFT\FullColorDisplayInterface;
use RealityInterface\Displays\EmbeddedDisplay;
use ScrapyardIO\NutsAndBolts\DataObjects\DumpedBuffer;
use ScrapyardIO\NutsAndBolts\DataObjects\FormatSpec;
use ScrapyardIO\NutsAndBolts\Enums\BitDepth;
use ScrapyardIO\NutsAndBolts\Enums\Endianness;
use ScrapyardIO\NutsAndBolts\Enums\PixelFormat;
use ScrapyardIO\NutsAndBolts\Enums\ScanDirection;
use Waveforms\Carriers\GPIO\GPIO;
use Waveforms\Carriers\SPI\SPI;

#[OutputsColor]
class GC9A01 extends EmbeddedDisplay implements FullColorDisplayInterface
{
    use GC9A01API;

    protected bool $booted = false;

    /**
     * @throws Exception
     */
    public function __construct(
        protected readonly GC9A01DataCarrier $carrier,
        int $width,
        int $height,
        GC9A01MADControl $mad_ctrl,
        protected GC9A01ColorMode $color_mode,
        GC9A01DisplayFunctionControl $display_fn_ctrl,
        GC9A01PowerControl2 $pwr_ctrl2,
        GC9A01PowerControl3 $pwr_ctrl3,
        GC9A01PowerControl4 $pwr_ctrl4,
        GC9A01FrameRate $frame_rate,
        GC9A01Gamma1 $gamma1,
        GC9A01Gamma2 $gamma2,
        GC9A01Gamma3 $gamma3,
        GC9A01Gamma4 $gamma4,
    ) {
        $this->boot(
            $mad_ctrl,
            $color_mode,
            $display_fn_ctrl,
            $pwr_ctrl2,
            $pwr_ctrl3,
            $pwr_ctrl4,
            $frame_rate,
            $gamma1,
            $gamma2,
            $gamma3,
            $gamma4,
        );
        parent::__construct($width, $height);
    }

    public function display(DumpedBuffer $buffer): void
    {
        $width = $buffer->width ?? $this->width();
        $height = $buffer->height ?? $this->height();
        $this->setAddressWindow($buffer->origin_x, $buffer->origin_y, $width, $height);
        $this->writeFrame($buffer->raw_data);
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, mixed $value): void
    {
        match ($name) {
            'display_on' => $this->setDisplay((bool) $value),
            'sleep_mode_enabled' => $this->setSleepMode((bool) $value),
            'mad_control' => $this->setMADControl($value),
            'pixel_format' => $this->setPixelFormat($value),
            'display_function_control' => $this->setDisplayFunctionControl($value),
            'power_control2' => $this->setPowerControl2($value),
            'power_control3' => $this->setPowerControl3($value),
            'power_control4' => $this->setPowerControl4($value),
            'frame_rate' => $this->setFrameRate($value),
            'display_inversion_enabled' => $this->setDisplayInversion((bool) $value),
            'tearing_effect_enabled' => $this->setTearingEffect((bool) $value),
            'color_gamma1' => $this->setGamma1($value),
            'color_gamma2' => $this->setGamma2($value),
            'color_gamma3' => $this->setGamma3($value),
            'color_gamma4' => $this->setGamma4($value),
            'normal_mode_on' => $this->setNormalDisplayMode((bool) $value),
            default => throw GC9A01Exception::invalidProperty($name)
        };
    }

    /**
     * Run the GC9A01 power-on sequence.
     *
     * The order is reproduced from the reference panel bring-up: unlock the
     * register banks, pour in the manufacturer magic (interleaved with the
     * handful of documented registers), then take the panel out of sleep and
     * turn it on.
     *
     * @throws Exception
     */
    protected function boot(
        GC9A01MADControl $mad_ctrl,
        GC9A01ColorMode $color_mode,
        GC9A01DisplayFunctionControl $display_fn_ctrl,
        GC9A01PowerControl2 $pwr_ctrl2,
        GC9A01PowerControl3 $pwr_ctrl3,
        GC9A01PowerControl4 $pwr_ctrl4,
        GC9A01FrameRate $frame_rate,
        GC9A01Gamma1 $gamma1,
        GC9A01Gamma2 $gamma2,
        GC9A01Gamma3 $gamma3,
        GC9A01Gamma4 $gamma4,
    ): void {
        if (! $this->booted) {
            $this->carrier->reset();

            // Unlock the manufacturer register banks (revealed secret pair).
            $this->interRegisterEnable2();
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_1);
            $this->interRegisterEnable1();
            $this->interRegisterEnable2();
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_1);

            // Undocumented bring-up block 0x84..0x8F.
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_2);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_3);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_4);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_5);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_6);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_7);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_8);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_9);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_10);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_11);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_12);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_13);

            // Documented framing registers.
            $this->setDisplayFunctionControl($display_fn_ctrl);
            $this->setMADControl($mad_ctrl);
            $this->setPixelFormat($color_mode);

            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_14);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_15);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_16);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_17);

            // Revealed secret power control registers.
            $this->setPowerControl($pwr_ctrl2, $pwr_ctrl3, $pwr_ctrl4);

            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_18);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_19);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_20);

            // Revealed secret gamma banks.
            $this->setColorControl($gamma1, $gamma2, $gamma3, $gamma4);

            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_21);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_22);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_23);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_24);

            // Revealed secret frame rate.
            $this->setFrameRate($frame_rate);

            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_25);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_26);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_27);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_28);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_29);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_30);
            $this->mystery(GC9A01OpCode::MYSTERY_REGISTER_31);

            $this->tearingEffectLineOn();
            $this->displayInversionOn();
            $this->sleepModeOff();
            $this->displayOn();

            $this->booted = true;
        }
    }

    public function generateFormatSpec(): FormatSpec
    {
        return new FormatSpec(
            PixelFormat::ROW_MAJOR,
            BitDepth::from($this->color_mode->bitsPerPixel()),
            ScanDirection::TOP_TO_BOTTOM,
            endianness: Endianness::MSB,
        );
    }

    /**
     * @throws Exception
     */
    public static function connection(string $driver): GC9A01Factory
    {
        return new GC9A01Factory(
            SPI::connection($driver),
            GPIO::connection($driver)
        );
    }
}
