<?php

declare(strict_types=1);

namespace ADS\ValueObjects\Implementation\Enum;

use ADS\ValueObjects\Exception\EnumException;
use ADS\ValueObjects\StringValue;

use function array_filter;
use function count;
use function is_string;
use function strval;

abstract class StringEnumValue extends EnumValue implements StringValue
{
    /**
     * @param mixed $value
     */
    protected function __construct($value)
    {
        if (! is_string($value)) {
            throw EnumException::wrongType(
                $value,
                'string',
                static::class
            );
        }

        parent::__construct($value);

        $noneStringValues = array_filter(
            $this->possibleValues,
            static fn ($possibleValue) => ! is_string($possibleValue)
        );

        if (count($noneStringValues) <= 0) {
            return;
        }

        throw EnumException::wrongPossibleValueTypes(
            $noneStringValues,
            'string',
            static::class
        );
    }

    /**
     * @return static
     */
    public static function fromString(string $value)
    {
        return static::fromValue($value);
    }

    public function toString(): string
    {
        return strval($this->toValue());
    }
}
