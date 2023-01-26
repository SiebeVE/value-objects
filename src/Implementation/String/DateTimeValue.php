<?php

declare(strict_types=1);

namespace ADS\ValueObjects\Implementation\String;

use ADS\ValueObjects\Exception\DateTimeException;
use ADS\ValueObjects\Implementation\RulesLogic;
use DateTime;
use DateTimeInterface;

use function strtotime;
use function strval;

class DateTimeValue implements \ADS\ValueObjects\DateTimeValue
{
    use RulesLogic;

    protected DateTimeInterface $value;

    final protected function __construct(DateTimeInterface $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): static
    {
        $time = strtotime($value);

        if ($time === false) {
            throw DateTimeException::noValidDateTime($value, static::class);
        }

        return new static(new DateTime('@' . $time));
    }

    public function toString(): string
    {
        return $this->toFormattedString(DateTimeInterface::RFC3339_EXTENDED);
    }

    public function toFormattedString(string $format): string
    {
        return $this->toDateTime()->format($format);
    }

    public static function fromDateTime(DateTimeInterface $value): static
    {
        return new static($value);
    }

    public function toDateTime(): DateTimeInterface
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toValue(): string
    {
        return $this->toString();
    }

    public static function fromValue(mixed $value): static
    {
        return static::fromString(strval($value));
    }

    public static function now(): static
    {
        return static::fromString('now');
    }

    public function isEqualTo(mixed $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->toDateTime()->getTimestamp() === $other->toDateTime()->getTimestamp();
    }
}
