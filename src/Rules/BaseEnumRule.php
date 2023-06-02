<?php

namespace Kwaadpepper\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kwaadpepper\Enum\BaseEnum;
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
     * @throws \Kwaadpepper\Enum\Exceptions\UnknownEnumClass If the class passed is not an enum.
     */
    public function __construct(string $enumClass)
    {
        if (!class_exists($enumClass) or !is_subclass_of($enumClass, BaseEnum::class)) {
            throw new UnknownEnumClass();
        }
        $this->enumClass = $enumClass;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return boolean
     * @phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
     * @phpcs:disable Squiz.Commenting.FunctionComment.TypeHintMissing
     */
    abstract public function passes($attribute, $value): bool;
    // phpcs:enable

    /**
     * Get the validation error message.
     *
     * @return string
     */
    abstract public function message(): string;
}
