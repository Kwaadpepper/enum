<?php

namespace Kwaadpepper\Enum\Traits;

use BadMethodCallException;
use Illuminate\Support\Facades\Log;
use Kwaadpepper\Enum\BaseEnum;
use TypeError;

/**
 * @property array $attributes Laravel model attributes
 * @property array $enumCasts Map attribute names to enum classes.
 * @method bool hasCast(string $key, array|string|null $types = null)
 * @method mixed castAttribute(string $key, mixed $value)
 */
trait CastsEnums
{

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if ($this->hasEnumCast($key)) {
            $value = $this->castToEnum($key, $value);
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        try {
            if ($enum = $this->castToEnum($key, $value) and $enum instanceof BaseEnum) {
                $this->attributes[$key] = $enum->value;
                return $this;
            }
        } catch (TypeError | BadMethodCallException $e) {
            Log::debug(\sprintf('%s : trait CastEnums error, %s', __CLASS__, $e->getMessage()));
            if (in_array(config('app.env'), ['debug', 'testing'])) {
                throw $e;
            }
        }
        // @codeCoverageIgnoreStart
        return parent::setAttribute($key, $value);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Determine whether an attribute should be cast to a enum.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasEnumCast($key): bool
    {
        // This can happen if this trait is added to the model
        // but no enum casts have been added yet
        if ($this->enumCasts === null) {
            return false;
        }

        return array_key_exists($key, $this->enumCasts) and
        is_subclass_of($this->enumCasts[$key], BaseEnum::class);
    }

    /**
     * Casts the given key to an enum instance
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return \Kwaadpepper\Enum\BaseEnum|mixed|null
     * @throws TypeError — If anything else than string or int is used.
     * @throws BadMethodCallException — If a matching definition cannot be found.
     */
    protected function castToEnum($key, $value)
    {
        if (
            $value === null or
            (\is_object($value) and \is_subclass_of($value, BaseEnum::class)) or
            !isset($this->enumCasts[$key])
        ) {
            return $value;
        }

        if ($this->hasCast($key)) {
            $value = $this->castAttribute($key, $value);
        }

        // try cast to int
        $value = (is_numeric($value) and floatval(intval($value)) === floatval($value)) ?
            (int)$value : $value;

        /** @var \Kwaadpepper\Enum\BaseEnum $enum */
        $enum = $this->enumCasts[$key];

        return $enum::make($value);
    }
}
