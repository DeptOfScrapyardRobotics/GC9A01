Introduction
============

PHP Package for the GC9A01 240x240 round full-color TFT display.

Compatible SPI Interfaces
===============
The GC9A01 display communicates with your device over SPI, the Serial Peripheral Interface.

You can interface with displays such as the GC9A01 with this package the following ways:
* A Linux Single-Board Computer's exposed GPIO pins using the dedicated SPI MOSI/SCK and CS pins as well as 2 GPIO pins for DC and RST.
* An MPSSE-enabled USB-to-Serial device such as an FT232H generally using D0 and SCK, D1 for MOSI, D2 for MISO and D3 for CS, D4 and D5 for RST/DC and connected to nearly any Linux or MacOS USB port.

Dependencies
=============
This package makes use of modules within:
* [The ScrapyardIO Framework](https://github.com/ScrapyardIO/framework)

This package also requires one of the following extensions in order to interface with SPI
* [POSI Extension v^0.4.0 or newer](https://github.com/php-io-extensions/posi)
* [FTDI Extension v^0.4.0 or newer](https://github.com/php-io-extensions/ftdi)

In addition, an extension wrapper package is needed

For ext-posi
* [Microscrap POSIX Package v0.4.0 or newer](https://github.com/microscrap/posix)
* [Microscrap Native SPI Package v0.4.0 or newer](https://github.com/microscrap/spi)
* [Microscrap Native GPIO Package v0.4.0 or newer](https://github.com/microscrap/gpio)

For ext-ftdi
* [Microscrap FTDI Package v0.4.0 or newer](https://github.com/microscrap/ftdi)
* [Microscrap MPSSE Package v0.4.0 or newer](https://github.com/microscrap/mpsse)

Installing from Composer
====================
Inside the root of your PHP Project, simply require the GC9A01 package from composer
```shell
composer require dept-of-scrapyard-robotics/gc9a01
```
Framework Configuration
====================
If you would like to use the ScrapyardIO Framework to bootstrap your display without
wasting lines configuring your display right in the script you can add your desired
configuration to scrapyard-io.php, such as in this example:

### SPI
```php

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\GC9A01;

return [
    'displays' => [
        // For Native Configurations 
        'gc9a01-native' => [
            'class_name' => GC9A01::class,
            'connection' => ['driver' => 'native'],
            'startup' => [
                'spi' => [0, 0],
                'gpiochip' => [0],
                'rst' => [24],
                'dc' => [22],
            ],
        ],
        // For USB Configurations
        'gc9a01-usb' => [
            'class_name' => GC9A01::class,
            'connection' => ['driver' => 'usb'],
            'startup' => [
                'spi' => ['ft232h', 0],
                'gpiochip' => ['ft232h'],
                'rst' => [0],
                'dc' => [1],
            ],
        ],        
    ]
];
```

Basic Usage
============

### Native (POSIX) SPI driver. (Single Board Computers)
```php

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\GC9A01;

$native_spi_display = GC9A01::connection('native')
    ->spi(0, 0)
    ->gpiochip(0)
    ->rst(24)
    ->dc(22)
    ->create()
```

### USB (MPSSE) driver using SPI. (Linux and MacOS)
```php

use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\GC9A01;

$usb_spi_display = GC9A01::connection('usb')
    ->spi('ft232h', 0)
    ->gpiochip('ft232h')
    ->rst(0)
    ->dc(1)
    ->create()
```

## Alternative Usage

### Using Through the Display Library (as a ColorTFTDisplay)
```php
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\GC9A01;
use RealityInterface\Displays\Applied\FullColorTFT\ColorTFTDisplay;

$gc9a01 = GC9A01::connection('usb')
    ->spi('ft232h', 0)
    ->gpiochip('ft232h')
    ->rst(0)
    ->dc(1)
    ->create()
    
$display = ColorTFTDisplay::as($gc9a01);

```

### Using Through the Display Framework (with an autoloaded config) (as a ColorTFTDisplay)
```php

use RealityInterface\Displays\Applied\FullColorTFT\ColorTFTDisplay;

$display = ColorTFTDisplay::using('gc9a01-usb');

```

Display API
==========
The setters in this API interface with the device directly (register writes), so
you can use property access while still working against the panel itself.

Readable Properties (Getters)
-----------------------------
There are no readable magic properties exposed for the GC9A01 in this package.

Writable Properties (Setters)
-----------------------------
* `$display->display_on = true;`
  Turns the panel on or off.

* `$display->sleep_mode_enabled = false;`
  Enters or exits sleep mode.

* `$display->mad_control = new GC9A01MADControl(...);`
  Sets memory access control (orientation / RGB-BGR / mirroring).

* `$display->pixel_format = GC9A01ColorMode::COLOR16;`
  Sets the pixel format (color depth).

* `$display->display_function_control = new GC9A01DisplayFunctionControl(...);`
  Sets the display function control register.

* `$display->power_control2 = new GC9A01PowerControl2(...);`
  Sets power control register 2.

* `$display->power_control3 = new GC9A01PowerControl3(...);`
  Sets power control register 3.

* `$display->power_control4 = new GC9A01PowerControl4(...);`
  Sets power control register 4.

* `$display->frame_rate = new GC9A01FrameRate(...);`
  Sets the frame rate.

* `$display->display_inversion_enabled = false;`
  Enables or disables display color inversion.

* `$display->tearing_effect_enabled = false;`
  Enables or disables the tearing-effect output line.

* `$display->color_gamma1 = new GC9A01Gamma1(...);`
  Sets gamma curve 1.

* `$display->color_gamma2 = new GC9A01Gamma2(...);`
  Sets gamma curve 2.

* `$display->color_gamma3 = new GC9A01Gamma3(...);`
  Sets gamma curve 3.

* `$display->color_gamma4 = new GC9A01Gamma4(...);`
  Sets gamma curve 4.

* `$display->normal_mode_on = true;`
  Returns the panel to normal display mode.

Drawing on the Display
============
Draw with a `Screen`, which wraps a `GFXRenderer` over a frame buffer matched to
the panel's `FormatSpec`, then ships the bytes on `render()`. A colour TFT uses a
`DirtyRegionsBuffer` (coalesces changed rectangles, one update per region).
Colors are 16-bit RGB565 integers (e.g. `0xF800` red, `0x07E0` green, `0x001F` blue).

The GC9A01 is a 240x240 round panel, so keep important content away from the
corners.

```php
use DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\GC9A01;
use Microscrap\GFX\PhpdaFruit\Buffers\DirtyRegionsBuffer;
use Microscrap\GFX\PhpdaFruit\GFXRenderer;
use RealityInterface\Displays\Applied\FullColorTFT\ColorTFTDisplay;
use RealityInterface\Displays\Screen;

$gc9a01 = GC9A01::connection('usb')
    ->spi('ft232h', 0)
    ->gpiochip('ft232h')
    ->rst(0)
    ->dc(1)
    ->create();

$display = ColorTFTDisplay::as($gc9a01);

$buffer = new DirtyRegionsBuffer($display->width(), $display->height(), $display->getFormatSpec());
$screen = new Screen($display, new GFXRenderer($buffer));

$center = intdiv($display->width(), 2);

$screen
    ->fill(0x0000)
    ->drawCircle($center, $center, $center - 1, 0xFFFF)
    ->fillCircle($center, $center, 30, 0xF800)
    ->setTextColor(0x07E0)
    ->setTextSize(3)
    ->setCursor($center - 60, $center - 12)
    ->print('GC9A01')
    ->render();
```
