<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255']
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $allowed = ['first_name', "last_name", 'address'];



        $validator->after(function (Validator $validator) use ($allowed) {
            if (empty($this->all())) {
                $validator->errors()->add(
                    'profile',
                    'No changes have been made'
                );
            }

            $unsupportedFields = array_diff($this->keys(), $allowed);

            if (!empty($unsupportedFields)) {
                // Rejected fields included
                foreach ($unsupportedFields as $field) {
                    $validator->errors()->add(
                        $field,
                        'This field is not allowed to update.'
                    );
                }
            }
        });
    }
}
