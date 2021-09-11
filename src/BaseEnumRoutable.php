<?php

namespace Kwaadpepper\Enum;

use BadMethodCallException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Kwaadpepper\Enum\Exceptions\NotImplementedException;

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
        throw new NotImplementedException('getRouteKey not implemented, should not be used.');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        throw new NotImplementedException('getRouteKeyName not implemented, should not be used.');
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
            return static::make(is_numeric($value) ? intval($value) : $value);
        } catch (BadMethodCallException $e) {
            // try string value after that
            try {
                return static::make($value);
            } catch (BadMethodCallException $e) {
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
        throw new NotImplementedException('resolveChildRouteBinding not implemented, should not be used.');
    }
}
