<?php

namespace ScrapyardIO\Displays\Color\GC9A01\Exceptions;

use Exception;
use ScrapyardIO\Support\Exceptions\DisplayIOException;

class GC9A01Exception extends DisplayIOException
{
    public static function invalidProtocol(string $name): static
    {
        return new static("Unsupported protocol '{$name}'.");
    }

    public static function pixelOutOfBounds(int $x): static
    {
        return new static("$x not a valid pixel index");
    }
}
