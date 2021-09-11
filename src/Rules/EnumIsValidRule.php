<?php

namespace Kwaadpepper\Enum\Rules;

use BadMethodCallException;
use TypeError;

class EnumIsValidRule extends BaseEnumRule
{

    private $messageType = 0;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
    public function passes($attribute, $value): bool
    {
        try {
            // try cast to int if is int
            $value = (is_numeric($value) and floatval(intval($value)) === floatval($value)) ?
                (int)$value : $value;
            forward_static_call([$this->enumClass, 'make'], $value);
        } catch (TypeError $e) {
            $this->messageType = 1;
            return false;
        } catch (BadMethodCallException $e) {
            $this->messageType = 2;
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
        $message = null;
        switch ($this->messageType) {
            case 1:
                $message = trans('enum::enum.isNotValid');
                break;
            case 2:
                $message = trans('enum::enum.notInList', [
                    'values' => implode(',', forward_static_call([$this->enumClass, 'toValues']))
                ]);
        }
        return \is_string($message) ? $message : 'Invalid Enum';
    }
}
