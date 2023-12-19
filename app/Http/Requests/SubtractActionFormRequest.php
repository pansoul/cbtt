<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubtractActionFormRequest extends FormRequest
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
            'subtract.initiator_id' => ['required', 'numeric', 'exists:users,id'],
            'subtract.amount' => ['required', 'numeric', 'gt:0'],
            'subtract.exec_time' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'subtract.initiator_id' => 'User',
            'subtract.amount' => 'Amount',
            'subtract.exec_time' => 'Execution time',
        ];
    }

    /**
     * Prepare inputs for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'subtract.frozen' => filter_var($this->subtract['frozen'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
        ]);
    }
}
