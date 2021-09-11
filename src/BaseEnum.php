<?php

namespace Kwaadpepper\Enum;

use BadMethodCallException;
use JsonSerializable;
use Kwaadpepper\Enum\Exceptions\DuplicateLabelsException;
use Kwaadpepper\Enum\Exceptions\DuplicateValuesException;
use Kwaadpepper\Enum\Exceptions\UnknownEnumProperty;
use ReflectionClass;
use TypeError;

/**
 * This Base Enum is the class which should
 * be extended to create an enumeration
 *
 * @property-read int|string $value The enum value, can be string or int
 * @property-read string $label The enum label, is a string
 */
abstract class BaseEnum implements JsonSerializable
{

    /**
     * @var string|int
     * @readonly
     */
    protected $value;

    /**
     * @var string
     * @readonly
     */
    protected $label;

    /** @var array<string, array<string, \Kwaadpepper\Enum\BaseEnumDefinition>> */
    private static $definitionCache = [];


    // -- ENUM MAGIC METHODS --

    /**
     * @param string $attribute
     * @return int|string
     * @throws UnknownEnumProperty
     */
    public function __get(string $attribute)
    {
        if ($attribute === 'value') {
            return $this->value;
        }
        if ($attribute === 'label') {
            return $this->label;
        }
        throw new UnknownEnumProperty($attribute);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return static
     * @throws TypeError              If anything else than string or int is used.
     * @throws BadMethodCallException If a matching definition cannot be found.
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceAfterLastUsed
    public static function __callStatic(string $name, array $arguments)
    {
        return static::make($name);
    }

    /**
     * Output the value as a string
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }


    // -- ENUM EXPORTATION METHODS --

    /** @return array<BaseEnum> */
    public static function toArray(): array
    {
        $array = [];
        foreach (static::resolveDefinition() as $definition) {
            $array[] = static::make($definition->value);
        }
        return $array;
    }

    /** @return array<int|string> */
    public static function toValues(): array
    {
        $array = [];
        foreach (static::resolveDefinition() as $definition) {
            $array[] = $definition->value;
        }
        return $array;
    }

    /** @return array<string> */
    public static function toLabels(): array
    {
        $array = [];
        foreach (static::resolveDefinition() as $definition) {
            $array[] = $definition->label;
        }
        return $array;
    }

    /**
     * Json serialize implementation.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->value
        ];
    }


    // -- DEFINITON METHODS --

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
     * Get the enum definition name
     *
     * @return string|null
     */
    public function getDefinition(): ?string
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
     * Parse a value to create its corresponding enumeration value.
     *
     * @param string|int $value
     * @return static
     * @throws TypeError              If anything else than string or int is used.
     * @throws BadMethodCallException If a matching definition cannot be found.
     */
    public static function make($value): BaseEnum
    {
        if (!(is_string($value) || is_int($value))) {
            $enumClass = static::class;
            throw new TypeError("Only string and integer are allowed values for enum $enumClass.");
        }

        $definition = static::findDefinition($value);

        if ($definition === null) {
            $enumClass = static::class;
            throw new BadMethodCallException(
                "There's no value $value defined for enum $enumClass, consider adding it in the docblock definition."
            );
        }

        $obj        = new static();
        $obj->value = $definition->value;
        $obj->label = $definition->label;

        return $obj;
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
     * Get the Enum name from class name
     *
     * @return string
     */
    public static function getEnumName()
    {
        $cls = explode('\\', static::class);
        return array_pop($cls);
    }


    // -- PRIVATE METHODS --

    /**
     * @param string|int $input
     * @return \Kwaadpepper\Enum\BaseEnumDefinition|null
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
     * @return \Kwaadpepper\Enum\BaseEnumDefinition[]
     */
    private static function resolveDefinition(): array
    {
        if (isset(self::$definitionCache[static::class])) {
            return self::$definitionCache[static::class];
        }

        $reflectionClass = new ReflectionClass(static::class);
        $docComment      = $reflectionClass->getDocComment();

        preg_match_all('/@method\s+static\s+self\s+([\w_]+)\(\)/', $docComment, $matches);

        $definition = [];
        $valueMap   = static::values();
        $labelMap   = static::labels();

        foreach ($matches[1] as $methodName) {
            $valueMap[$methodName]   = $valueMap[$methodName] ?? $methodName;
            $labelMap[$methodName]   = $labelMap[$methodName] ?? $methodName;
            $definition[$methodName] = new BaseEnumDefinition(
                $methodName,
                $valueMap[$methodName],
                $labelMap[$methodName]
            );
        }

        if (self::arrayHasDuplicates($valueMap)) {
            throw new DuplicateValuesException();
        }

        if (self::arrayHasDuplicates($labelMap)) {
            throw new DuplicateLabelsException();
        }

        return self::$definitionCache[static::class] = $definition;
    }

    /**
     * Check if an array has duplicated values
     *
     * @param array $array
     * @return boolean True if it has duplicated value.
     */
    private static function arrayHasDuplicates(array $array): bool
    {
        return count($array) > count(array_unique($array));
    }
}
