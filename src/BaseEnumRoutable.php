<?php

namespace Kwaadpepper\Enum;

use BadMethodCallException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Kwaadpepper\Enum\Exceptions\EnumNotRoutableException;
use Kwaadpepper\Enum\Exceptions\NotImplementedException;
use TypeError;

/**
 * This Base Enum will be routable with laravel.
 * So you can use it with service container passing an
 * enumeration as any route parameter directly as
 * a controller parameter.
 */
abstract class BaseEnumRoutable extends BaseEnum implements UrlRoutable
{
    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->value;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        // @codeCoverageIgnoreStart
        throw new NotImplementedException('getRouteKeyName not implemented, should not be used.');
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Kwaadpepper\Enum\BaseEnum|null
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
    public function resolveRouteBinding($value, $field = null)
    {
        // try to cast numeric value first
        try {
            // try cast to int
            $iValue = (is_numeric($value) and floatval(intval($value)) === floatval($value)) ?
                (int)$value : $value;
            return static::make($iValue);
        } catch (BadMethodCallException | EnumNotRoutableException | TypeError $e) {
            // try string value after that
            try {
                return static::make($value);
            } catch (BadMethodCallException | EnumNotRoutableException | TypeError $e) {
                // could not find a suitable value
                return null;
            }
        }
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws NotImplementedException
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // @codeCoverageIgnoreStart
        throw new NotImplementedException('resolveChildRouteBinding not implemented, should not be used.');
        // @codeCoverageIgnoreEnd
    }
}
