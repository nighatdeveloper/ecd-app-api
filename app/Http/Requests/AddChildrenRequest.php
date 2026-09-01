<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AddChildrenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_children' => ['required', 'integer', 'min:0'],
            'total_daughters' => ['required', 'integer', 'min:0'],
            'total_sons' => ['required', 'integer', 'min:0'],
            'total_transgender' => ['required', 'integer', 'min:0'],

            'children' => ['required', 'array'],

            'children.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'children.*.gender' => [
                'required',
                Rule::in(['daughter', 'son', 'transgender']),
            ],

            'children.*.dob' => [
                'required',
                'date',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $data = $this->all();

            /*
             * 1. Gender counts ka total check
             */
            $sum =
                (int) ($data['total_daughters'] ?? 0)
                + (int) ($data['total_sons'] ?? 0)
                + (int) ($data['total_transgender'] ?? 0);

            if (
                (int) ($data['total_children'] ?? -1) !== $sum
            ) {
                $validator->errors()->add(
                    'total_children',
                    'total_children must equal total_daughters + total_sons + total_transgender.'
                );
            }

            /*
             * 2. Child details ka count check
             *
             * Example:
             * total_children = 4
             * children details = 2
             *
             * Result: ERROR
             */
            $childrenCount = count($data['children'] ?? []);

            if (
                isset($data['total_children'])
                && $childrenCount !== (int) $data['total_children']
            ) {
                $validator->errors()->add(
                    'children',
                    'You specified ' .
                    $data['total_children'] .
                    ' total children but provided details for only ' .
                    $childrenCount .
                    ' children.'
                );
            }
        });
    }
}
