<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Factory;

use BareMetal\CircuitFactory;
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Adapters\GC9A01SPIDevice;
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
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\GC9A01;
use Exception;
use Waveforms\Carriers\GPIO\Factory\GPIOConnectionBuilder;
use Waveforms\Carriers\GPIO\GPIOPin;
use Waveforms\Carriers\SPI\Enums\SPIMode;
use Waveforms\Carriers\SPI\Factory\SPIConnectionBuilder;

class GC9A01Factory extends CircuitFactory
{
    protected bool $has_dc = false;

    protected bool $has_rst = false;

    protected int $width = 240;

    protected int $height = 240;

    protected int $max_packet_size = 2048;

    public string $consumer = 'gc9a01';

    public GC9A01MADControl $mad_ctrl;

    public GC9A01ColorMode $color_mode = GC9A01ColorMode::COLOR16;

    public GC9A01DisplayFunctionControl $display_fn_ctrl;

    public GC9A01PowerControl2 $power_control_2;

    public GC9A01PowerControl3 $power_control_3;

    public GC9A01PowerControl4 $power_control_4;

    public GC9A01FrameRate $frame_rate;

    public GC9A01Gamma1 $gamma1;

    public GC9A01Gamma2 $gamma2;

    public GC9A01Gamma3 $gamma3;

    public GC9A01Gamma4 $gamma4;

    public ?SPIConnectionBuilder $connection = null;

    public function __construct(
        public SPIConnectionBuilder $spi_connection,
        public GPIOConnectionBuilder $gpio_connection
    ) {
        $this->mad_ctrl = new GC9A01MADControl;
        $this->display_fn_ctrl = new GC9A01DisplayFunctionControl;
        $this->power_control_2 = new GC9A01PowerControl2;
        $this->power_control_3 = new GC9A01PowerControl3;
        $this->power_control_4 = new GC9A01PowerControl4;
        $this->frame_rate = new GC9A01FrameRate;
        $this->gamma1 = new GC9A01Gamma1;
        $this->gamma2 = new GC9A01Gamma2;
        $this->gamma3 = new GC9A01Gamma3;
        $this->gamma4 = new GC9A01Gamma4;
    }

    public function spi(string|int $master, int $chip_select): static
    {
        $this->connection = $this->spi_connection->firstly($master)
            ->chip($chip_select)
            ->speed(32000000)
            ->mode(SPIMode::MODE_0);

        return $this;
    }

    public function gpiochip(int|string $chip): static
    {
        $this->gpio_connection = $this->gpio_connection->firstly($chip);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function dc(int $pin): static
    {
        if (! $this->has_dc) {
            $gpio_output = GPIOPin::createOutput($this->connection->connection(), $pin, 'dc');
            $this->gpio_connection = $this->gpio_connection->addOutput($gpio_output);
            $this->has_dc = true;
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function rst(int $pin): static
    {
        if (! $this->has_rst) {
            $gpio_output = GPIOPin::createOutput($this->connection->connection(), $pin, 'rst');
            $this->gpio_connection = $this->gpio_connection->addOutput($gpio_output);
            $this->has_rst = true;
        }

        return $this;
    }

    public function consumer(string $consumer): static
    {
        $this->consumer = $consumer;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function maxPacketSize(int $max_packet_size): static
    {
        $this->max_packet_size = $max_packet_size;

        return $this;
    }

    public function madControl(GC9A01MADControl $control): static
    {
        $this->mad_ctrl = $control;

        return $this;
    }

    public function colorMode(GC9A01ColorMode $color_mode): static
    {
        $this->color_mode = $color_mode;

        return $this;
    }

    public function displayFunctionControl(GC9A01DisplayFunctionControl $control): static
    {
        $this->display_fn_ctrl = $control;

        return $this;
    }

    public function powerControl2(GC9A01PowerControl2 $control): static
    {
        $this->power_control_2 = $control;

        return $this;
    }

    public function powerControl3(GC9A01PowerControl3 $control): static
    {
        $this->power_control_3 = $control;

        return $this;
    }

    public function powerControl4(GC9A01PowerControl4 $control): static
    {
        $this->power_control_4 = $control;

        return $this;
    }

    public function frameRate(GC9A01FrameRate $frame_rate): static
    {
        $this->frame_rate = $frame_rate;

        return $this;
    }

    public function gamma1(GC9A01Gamma1 $gamma): static
    {
        $this->gamma1 = $gamma;

        return $this;
    }

    public function gamma2(GC9A01Gamma2 $gamma): static
    {
        $this->gamma2 = $gamma;

        return $this;
    }

    public function gamma3(GC9A01Gamma3 $gamma): static
    {
        $this->gamma3 = $gamma;

        return $this;
    }

    public function gamma4(GC9A01Gamma4 $gamma): static
    {
        $this->gamma4 = $gamma;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function create(): GC9A01
    {
        $carrier = $this->connection?->boot();
        if (is_null($carrier)) {
            throw new Exception('A connection was not registered.');
        }

        $gpio = $this->gpio_connection
            ->shareConnectionWith($carrier)
            ->consumer($this->consumer)
            ->boot();

        $carrier = new GC9A01SPIDevice($carrier, $gpio, $this->max_packet_size);

        return new GC9A01(
            $carrier,
            $this->width,
            $this->height,
            $this->mad_ctrl,
            $this->color_mode,
            $this->display_fn_ctrl,
            $this->power_control_2,
            $this->power_control_3,
            $this->power_control_4,
            $this->frame_rate,
            $this->gamma1,
            $this->gamma2,
            $this->gamma3,
            $this->gamma4,
        );
    }
}
