<?php

namespace ScrapyardIO\Displays\Color\GC9A01\Concerns;

use ScrapyardIO\Transports\SPITransport;
use ScrapyardIO\Transports\Concerns\ResetPin;
use ScrapyardIO\Transports\Concerns\DataCommandPin;

trait GC9A01SPIChip
{
    use DataCommandPin, ResetPin;

    protected ?SPITransport $gc9a01_spi = null;
    protected int $gc9a01_spi_bus = 0;
    protected int $spi_gc9a01_chip_select = 0;
    protected int $max_packet_size = 0;

    abstract public function wait(int $ms): void;

    protected function spi_gc9a01_bus(?int $bus = null): int
    {
        if(!is_null($bus))
        {
            $this->gc9a01_spi_bus = $bus;
        }
        return $this->gc9a01_spi_bus;
    }

    protected function spi_gc9a01_chip_select(?int $cs = null): int
    {
        if($cs)
        {
            $this->spi_gc9a01_chip_select = $cs;
        }
        return $this->spi_gc9a01_chip_select;
    }

    protected function gc9a01_spi(): ?SPITransport
    {
        if(empty($this->gc9a01_spi))
        {
            $this->gc9a01_spi = new SPITransport(
                $this->spi_gc9a01_bus(),
                $this->spi_gc9a01_chip_select(),
                0,
                40000000,
                0
            );
        }

        return $this->gc9a01_spi;
    }

    public function sendData(array $bytes): void
    {
        $this->dcHigh();
        $this->wait(1);
        $this->gc9a01_spi()->send($bytes);
    }

    public function sendCommand(array $bytes): void
    {
        $this->dcLow();
        if(count($bytes) > 1)
        {
            $command = $bytes[0];
            $this->gc9a01_spi()->send([$command]);
            unset($bytes[0]);
            $payload = array_values($bytes);
            $this->sendData($payload);
        }
        else
        {
            $this->gc9a01_spi()->send($bytes);
        }
    }

    protected function resetSequence(): void
    {
        $this->rstHigh();
        $this->wait(10);

        $this->rstLow();
        $this->wait(10);

        $this->rstHigh();
        $this->wait(120);

        $this->dcLow();
    }
}
