<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Free text, no fixed list - matches the doc's example values.
            'gender' => ['required', 'string', 'max:50'],
            'age' => ['required', 'integer', 'min:0', 'max:150'],
        ];
    }
}
