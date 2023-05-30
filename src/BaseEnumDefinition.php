<?php

namespace Kwaadpepper\Enum;

/**
 * The definition of an enumeration
 * This defines it's value, label and method name
 */
class BaseEnumDefinition
{
    /** @var string|integer */
    public $value;

    /** @var string */
    public $label;

    /** @var string */
    private $methodName;

    /**
     * @param string         $methodName
     * @param string|integer $value
     * @param string         $label
     */
    public function __construct(string $methodName, $value, string $label)
    {
        $this->methodName = strtolower($methodName);
        $this->value      = $value;
        $this->label      = $label;
    }

    /**
     * @param string|integer $input
     * @return boolean
     */
    public function equals($input): bool
    {
        if ($this->value === $input) {
            return true;
        }
        if (is_string($input) and $this->methodName === strtolower($input)) {
            return true;
        }
        return false;
    }
}
