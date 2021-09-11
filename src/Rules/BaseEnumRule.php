<?php

namespace Kwaadpepper\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kwaadpepper\Enum\Exceptions\UnknownEnumClass;

abstract class BaseEnumRule implements Rule
{

    /** @var string $enum */
    protected $enumClass;

    /**
     * Create a new rule instance
     * which validate the fact that a value is within an Enum
     *
     * @param string $enumClass
     * @return void
     */
    public function __construct(string $enumClass)
    {
        if (!class_exists($enumClass)) {
            throw new UnknownEnumClass();
        }
        $this->enumClass = $enumClass;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    abstract public function passes($attribute, $value): bool;

    /**
     * Get the validation error message.
     *
     * @return string
     */
    abstract public function message(): string;
}
