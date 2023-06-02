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
     * @phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn
     *
     * @return string
     * @throws \Kwaadpepper\Enum\Exceptions\NotImplementedException GetRouteKeyName not implemented,
     *                                                              should not be used.
     */
    public function getRouteKeyName()
    {
        // phpcs:enable
        // @codeCoverageIgnoreStart
        throw new NotImplementedException('GetRouteKeyName not implemented, should not be used.');
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed       $value
     * @param  string|null $field
     * @return \Kwaadpepper\Enum\BaseEnum|null
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
     */
    public function resolveRouteBinding(mixed $value, $field = null)
    {
        // phpcs:enable
        // Try to cast numeric value first.
        try {
            // Try cast to int.
            $iValue = (is_numeric($value) and floatval(intval($value)) === floatval($value)) ?
                (int)$value : $value;
            return static::make($iValue);
        } catch (BadMethodCallException | EnumNotRoutableException | TypeError $e) {
            // Try string value after that.
            try {
                return static::make($value);
            } catch (BadMethodCallException | EnumNotRoutableException | TypeError $e) {
                // Could not find a suitable value.
                return null;
            }
        }
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn
     *
     * @param  string      $childType
     * @param  mixed       $value
     * @param  string|null $field
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Kwaadpepper\Enum\Exceptions\NotImplementedException ResolveChildRouteBinding not implemented,
     *                                                              should not be used.
     * @phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
     * @phpcs:disable Squiz.Commenting.FunctionComment.TypeHintMissing
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // phpcs:enable
        // @codeCoverageIgnoreStart
        throw new NotImplementedException('ResolveChildRouteBinding not implemented, should not be used.');
        // @codeCoverageIgnoreEnd
    }
}
