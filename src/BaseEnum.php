<?php

namespace Kwaadpepper\Enum;

use Kwaadpepper\Enum\Exceptions\DuplicateDefinitionException;
use Kwaadpepper\Enum\Exceptions\DuplicateLabelsException;
use Kwaadpepper\Enum\Exceptions\DuplicateValuesException;
use Kwaadpepper\Enum\Exceptions\EmptyDefinitionException;
use Kwaadpepper\Enum\Exceptions\EnumNotRoutableException;
use Kwaadpepper\Enum\Exceptions\UnknownEnumProperty;

/**
 * This Base Enum is the class which should
 * be extended to create an enumeration
 *
 * @property-read int|string $value The enum value, can be string or int
 * @property-read string $label The enum label, is a string
 */
abstract class BaseEnum implements \JsonSerializable
{
    /**
     * @var string|integer
     */
    protected $value;

    /**
     * @var string
     */
    protected $label;

    /** @var array<string, array<string, \Kwaadpepper\Enum\BaseEnumDefinition>> */
    private static $definitionCache = [];


    // -- ENUM MAGIC METHODS --

    /**
     * @param integer|string $value
     * @param string         $label
     * @throws \TypeError                                            If anything else than string or int
     *                                                               is used for value.
     * @throws \Kwaadpepper\Enum\Exceptions\EnumNotRoutableException If provided value are null.
     * @phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
     */
    public function __construct($value = null, string $label = null)
    {
        // phpcs:ignore
        /**
         * This cannot be allowed, happens with laravel resolver
         * when using BaseEnum instead of BaseEnumRoutable
         */
        if ($label === null and !is_subclass_of(static::class, BaseEnumRoutable::class)) {
            throw new EnumNotRoutableException();
        }
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * @param string $attribute
     * @return integer|string
     * @throws \Kwaadpepper\Enum\Exceptions\UnknownEnumProperty As it could not be found.
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
     * @param array  $arguments
     * @return static
     * @throws \TypeError              If anything else than string or int is used.
     * @throws \BadMethodCallException If a matching definition cannot be found.
     * @throws \Kwaadpepper\Enum\Exceptions\EnumNotRoutableException If provided value are null.
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceAfterLastUsed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        // phpcs:enable
        return static::make($name);
    }

    /**
     * Output the value as a string
     *
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
     * Get all values
     *
     * @return array<string, string|int> | Closure(string):(int|string)
     */
    protected static function values()
    {
        return [];
    }

    /**
     * Get all labels
     *
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
     * @param string|integer $value
     * @return static
     * @throws TypeError              If anything else than string or int is used.
     * @throws \BadMethodCallException If a matching definition cannot be found.
     * @throws \Kwaadpepper\Enum\Exceptions\EnumNotRoutableException If provided value are null.
     * @phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
     */
    public static function make($value): BaseEnum
    {
        // phpcs:enable
        self::assertValidValue($value);

        $definition = static::findDefinition($value);

        if ($definition === null) {
            $enumClass = static::class;
            throw new \BadMethodCallException(
                "There's no value $value defined for enum $enumClass, consider adding it in the docblock definition."
            );
        }

        $obj = new static(
            $definition->value,
            $definition->label
        );

        return $obj;
    }

    /**
     * Test if an enum values is equal to another
     *
     * @param \Kwaadpepper\Enum\BaseEnum $other
     * @return boolean
     */
    public function equals(BaseEnum $other): bool
    {
        if (
            get_class($this) === get_class($other)
            && $this->value === $other->value
        ) {
            return true;
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
     * @param string|integer $input
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
     * Resolve enum defintion
     *
     * @return \Kwaadpepper\Enum\BaseEnumDefinition[]
     * @throws \Kwaadpepper\Enum\Exceptions\EmptyDefinitionException If there is any.
     * @throws \Kwaadpepper\Enum\Exceptions\DuplicateValuesException If there is any in labels map.
     * @throws \Kwaadpepper\Enum\Exceptions\DuplicateValuesException If there is any in values map.
     * @phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
     */
    private static function resolveDefinition(): array
    {
        // phpcs:enable
        if (isset(self::$definitionCache[static::class])) {
            return self::$definitionCache[static::class];
        }

        $reflectionClass = new \ReflectionClass(static::class);
        $docComment      = $reflectionClass->getDocComment();

        preg_match_all('/@method\s+static\s+self\s+([\w_]+)\(\)/', $docComment, $matches);

        $definition = [];
        $valueMap   = static::values();
        $labelMap   = static::labels();

        if (!count($matches[1])) {
            throw new EmptyDefinitionException('You are missing defining enum definition in comments.');
        }
        if (self::arrayHasDuplicates($matches[1])) {
            throw new DuplicateDefinitionException('You have duplicate values in comments enum definition.');
        }

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
            throw new DuplicateValuesException('You have duplicates values in enum values method.');
        }

        if (self::arrayHasDuplicates($labelMap)) {
            throw new DuplicateLabelsException('You have duplicates values in enum labels method.');
        }

        self::$definitionCache[static::class] = $definition;

        return self::$definitionCache[static::class];
    }

    /**
     * Check if an array has duplicated values
     *
     * @param array $array
     * @return boolean True if it has duplicated value.
     */
    private static function arrayHasDuplicates(array $array): bool
    {
        $uniqueVals = [];
        foreach ($array as $value) {
            if (!in_array($value, $uniqueVals, true)) {
                $uniqueVals[] = $value;
            }
        }
        return count($array) > count($uniqueVals);
    }

    /**
     * Assert value is valid
     *
     * @param mixed $value
     * @return void
     * @throws \TypeError If anything else than string or int is used.
     */
    private static function assertValidValue(mixed $value): void
    {
        if (!(is_string($value) || is_int($value))) {
            $enumClass = static::class;
            throw new \TypeError("Only string and integer are allowed values for enum $enumClass.");
        }
    }
}
