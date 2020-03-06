<?php

declare(strict_types=1);

namespace ADS\ValueObjects;

interface EnumValue extends ValueObject
{
    /**
     * @return array<string>
     */
    public static function possibleValues() : array;

    /**
     * @param mixed $value
     *
     * @return static
     */
    public static function fromValue($value);

    /**
     * @return mixed
     */
    public function toValue();
}
