<?php

namespace Kwaadpepper\Enum;

use BadMethodCallException;
use Closure;
use Illuminate\Contracts\Routing\UrlRoutable;
use JsonSerializable;
use Kwaadpepper\Enum\Exceptions\DuplicateLabelsException;
use Kwaadpepper\Enum\Exceptions\DuplicateValuesException;
use Kwaadpepper\Enum\Exceptions\NotImplementedException;
use Kwaadpepper\Enum\Exceptions\UnknownEnumProperty;
use ReflectionClass;
use TypeError;

abstract class BaseEnum implements JsonSerializable, UrlRoutable
{
    /**
     * @var string|int
     * @readonly
     */
    protected $value;

    /** @readonly */
    protected $label;

    /** @var array<string, array<string, App\Enums\BaseEnumDefinition>> */
    private static $definitionCache = [];

    /**
     * @return array[BaseEnum]
     */
    public static function toArray(): array
    {
        $array = [];
        foreach (static::resolveDefinition() as $definition) {
            $array[] = static::make($definition->value);
        }
        return $array;
    }

    /**
     * @return string[]|int[]
     */
    public static function toValues(): array
    {
        $array = [];
        foreach (static::resolveDefinition() as $definition) {
            $array[] = $definition->value;
        }
        return $array;
    }

    /**
     * @return string[]
     */
    public static function toLabels(): array
    {
        $array = [];
        foreach (static::resolveDefinition() as $definition) {
            $array[] = $definition->label;
        }
        return $array;
    }

    /**
     * Get the enum definition
     *
     * @return mixed|null
     */
    public function getDefinition()
    {
        $className = static::class;
        foreach (self::$definitionCache[$className] as $definition => $enum) {
            if ($enum->value === $this->value) {
                return $definition;
            }
        }
        return null;
    }

    /**
     * @param string|int $value
     * @throws TypeError
     * @throws BadMethodCallException
     * @return static
     */
    public static function make($value): BaseEnum
    {
        if (!(is_string($value) || is_int($value))) {
            $enumClass = static::class;
            throw new TypeError("Only string and integer are allowed values for enum {$enumClass}.");
        }

        $definition = static::findDefinition($value);

        if ($definition === null) {
            $enumClass = static::class;
            throw new BadMethodCallException(
                "There's no value {$value} defined for enum {$enumClass}," .
                "consider adding it in the docblock definition."
            );
        }

        $obj = new static();
        $obj->value = $definition->value;
        $obj->label = $definition->label;

        return $obj;
    }

    /**
     * @param string $name
     *
     * @return int|string
     *
     * @throws UnknownEnumProperty
     */
    public function __get(string $name)
    {
        if ($name === 'label') {
            return $this->label;
        }

        if ($name === 'value') {
            return $this->value;
        }

        throw new UnknownEnumProperty($name);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return static
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return static::make($name);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return bool|mixed
     *
     * @throws UnknownEnumMethod
     */
    public function __call(string $name, array $arguments)
    {
        if (strpos($name, 'is') === 0) {
            $other = static::make(substr($name, 2));
            return $this->equals($other);
        }

        return self::__callStatic($name, $arguments);
    }

    public function equals(BaseEnum ...$others): bool
    {
        foreach ($others as $other) {
            // @phpcs:disable PSR2.ControlStructures.ControlStructureSpacing.SpacingAfterOpenBrace
            if (
                get_class($this) === get_class($other)
                && $this->value === $other->value
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]|int[]|Closure
     * @return array<string, string|int> | Closure(string):(int|string)
     */
    protected static function values()
    {
        return [];
    }

    /**
     * @return string[]|Closure
     * @return array<string, string> | Closure(string):string
     */
    protected static function labels()
    {
        return [];
    }

    /**
     * @param string|int $input
     *
     * @return App\Enums\BaseEnumDefinition|null
     */
    private static function findDefinition($input): ?BaseEnumDefinition
    {
        foreach (static::resolveDefinition() as $definition) {
            if ($definition->equals($input)) {
                return $definition;
            }
        }

        return null;
    }

    /**
     * @return App\Enums\BaseEnumDefinition[]
     */
    private static function resolveDefinition(): array
    {
        $className = static::class;

        if (self::$definitionCache[$className] ?? null) {
            return self::$definitionCache[$className];
        }

        $reflectionClass = new ReflectionClass($className);

        $docComment = $reflectionClass->getDocComment();

        preg_match_all('/@method\s+static\s+self\s+([\w_]+)\(\)/', $docComment, $matches);

        $definition = [];

        $valueMap = static::values();

        if ($valueMap instanceof Closure) {
            $valueMap = array_map($valueMap, array_combine($matches[1], $matches[1]));
        }

        $labelMap = static::labels();

        if ($labelMap instanceof Closure) {
            $labelMap = array_map($labelMap, array_combine($matches[1], $matches[1]));
        }

        foreach ($matches[1] as $methodName) {
            $value = $valueMap[$methodName] = $valueMap[$methodName] ?? $methodName;

            $label = $labelMap[$methodName] = $labelMap[$methodName] ?? $methodName;

            $definition[$methodName] = new BaseEnumDefinition($methodName, $value, $label);
        }

        if (self::arrayHasDuplicates($valueMap)) {
            throw new DuplicateValuesException();
        }

        if (self::arrayHasDuplicates($labelMap)) {
            throw new DuplicateLabelsException();
        }

        return self::$definitionCache[$className] = self::$definitionCache[$className] ?? $definition;
    }

    private static function arrayHasDuplicates(array $array): bool
    {
        return count($array) > count(array_unique($array));
    }

    /**
     * @return int|string
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'label' => $this->label
        ];
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        throw new NotImplementedException('getRouteKey not implemented, should not be used');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        throw new NotImplementedException('getRouteKeyName not implemented, should not be used');
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // try to cast numeric value first
        try {
            return static::make(is_numeric($value) ? intval($value) : $value);
        } catch (BadMethodCallException $e) {
        }
        // try string value after that
        try {
            return static::make($value);
        } catch (BadMethodCallException $e) {
        }
        // could not find a suitable value
        return null;
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
        throw new NotImplementedException('resolveChildRouteBinding not implemented, should not be used');
    }

    /**
     * Get the Enum name from class name
     *
     * @return string
     */
    public static function getEnumName()
    {
        $cls = explode('\\', static::class);
        return array_pop($cls);
    }
}
