<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kwaadpepper\Enum\Rules\EnumIsValidRule;
use Kwaadpepper\Enum\Tests\Enums\Days;

class PassDayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day' => ['required', new EnumIsValidRule(Days::class)],
        ];
    }
}
