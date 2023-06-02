<?php

namespace Kwaadpepper\Enum\Rules;

use BadMethodCallException;
use TypeError;

/**
 * Custom Rule to validate an enum
 */
class EnumIsValidRule extends BaseEnumRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return boolean
     * @phpcs:disable Squiz.Commenting.FunctionComment.TypeHintMissing
     * @phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
     */
    public function passes($attribute, $value): bool
    {
        // phpcs:enable
        try {
            // Try cast to int if is int.
            $value = (is_numeric($value) and floatval(intval($value)) === floatval($value)) ?
                (int)$value : $value;
            forward_static_call([$this->enumClass, 'make'], $value);
        } catch (TypeError | BadMethodCallException $e) {
            return false;
        }
        return true;
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('enum::enum.notInList', [
            'values' => implode(',', forward_static_call([$this->enumClass, 'toValues']))
        ]);
    }
}
