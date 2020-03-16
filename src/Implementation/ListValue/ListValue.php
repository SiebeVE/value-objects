<?php

declare(strict_types=1);

namespace ADS\ValueObjects\Implementation\ListValue;

use ADS\ValueObjects\Exception\InvalidListException;
use ADS\ValueObjects\ValueObject;
use function array_diff;
use function array_map;
use function array_pop;
use function array_push;
use function count;
use function get_class;
use function gettype;
use function is_scalar;
use function method_exists;
use function print_r;

abstract class ListValue implements \ADS\ValueObjects\ListValue
{
    /** @var mixed[] */
    protected array $value;

    /**
     * @param array<int,mixed> $value
     */
    protected function __construct(...$value) // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $value)
    {
        return static::fromItems(...array_map(
            static fn ($array) => static::fromArrayToItem($array),
            $value
        ));
    }

    /**
     * @inheritDoc
     */
    public static function fromItems(...$value) // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    {
        self::checkTypes($value);

        return new static(...$value);
    }

    /**
     * @inheritDoc
     */
    public static function emptyList()
    {
        return new static();
    }

    /**
     * @return array<mixed>
     */
    public function toArray() : array
    {
        return array_map(
            static fn($item) => static::fromItemToArray($item),
            $this->value
        );
    }

    /**
     * @return array<mixed>
     */
    public function toItems() : array
    {
        return $this->value;
    }

    public function __toString() : string
    {
        return print_r($this->toArray(), true);
    }

    /**
     * @inheritDoc
     */
    public function isEqualTo($other) : bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return empty(
            array_diff(
                $this->toArray(),
                $other->toArray()
            )
        )
            && empty(
                array_diff(
                    $other->toArray(),
                    $this->toArray()
                )
            );
    }

    /**
     * @inheritDoc
     */
    public function push($item)
    {
        $clone = clone $this;

        array_push($clone->value, static::toItem($item));

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function pop()
    {
        $clone = clone $this;

        array_pop($clone->value);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function contains($item) : bool
    {
        $item = static::toItem($item);

        foreach ($this->toItems() as $existingItem) {
            if ($existingItem instanceof ValueObject && $existingItem->isEqualTo($item)) {
                return true;
            }

            if ($existingItem === $item) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return $this->value[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        return $this->value[$this->count() - 1] ?? null;
    }

    public function count() : int
    {
        return count($this->value);
    }

    /**
     * @param mixed $value
     */
    private static function checkTypes($value) : void
    {
        if (! method_exists(static::class, '__itemType')) {
            return;
        }

        $type = static::__itemType();

        foreach ($value as $item) {
            if (! $item instanceof $type) {
                throw InvalidListException::noValidItemType(
                    is_scalar($item) ? gettype($item) : get_class($item),
                    $type,
                    static::class
                );
            }
        }
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    private static function toItem($item)
    {
        try {
            self::checkTypes([$item]);
        } catch (InvalidListException $exception) {
            $item = static::fromArrayToItem($item);
        }

        return $item;
    }
}