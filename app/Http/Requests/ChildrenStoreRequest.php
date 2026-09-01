<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChildrenStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_children' => 'required|integer|min:0',
            'total_daughters' => 'required|integer|min:0',
            'total_sons' => 'required|integer|min:0',
            'total_transgender' => 'required|integer|min:0',

            'children' => 'required|array',
            'children.*.name' => 'required|string|max:255',
            'children.*.gender' => 'required|in:daughter,son,transgender',
            'children.*.dob' => 'required|date|before_or_equal:today',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $total = (int) $this->total_children;

            $genderTotal =
                (int) $this->total_daughters +
                (int) $this->total_sons +
                (int) $this->total_transgender;

            // Total children must match gender-wise total
            if ($total !== $genderTotal) {
                $validator->errors()->add(
                    'total_children',
                    'Total children must equal total daughters + total sons + total transgender children.'
                );
            }

            // Total children must match actual children array
            if ($total !== count($this->children ?? [])) {
                $validator->errors()->add(
                    'children',
                    'Total children must match the number of children provided.'
                );
            }
        });
    }
}