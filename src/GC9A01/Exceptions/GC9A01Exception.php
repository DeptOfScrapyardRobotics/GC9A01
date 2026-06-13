<?php

namespace DeptOfScrapyardRobotics\Displays\GC9A01\GC9A01\Exceptions;

use RuntimeException;

class GC9A01Exception extends RuntimeException
{
    public static function invalidProperty(string $name): static
    {
        return new static("Invalid property $name");
    }

    public static function invalidRegisterValue(string $field, int $value, int $min, int $max): static
    {
        return new static("Valid $field values are between $min and $max, you input $value.");
    }
}
