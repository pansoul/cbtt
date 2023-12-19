<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddActionFormRequest extends FormRequest
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
            'add.initiator_id' => ['required', 'numeric', 'exists:users,id'],
            'add.amount' => ['required', 'numeric', 'gt:0'],
            'add.exec_time' => ['nullable', 'numeric', 'min:0'],
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
            'add.initiator_id' => 'User',
            'add.amount' => 'Amount',
            'add.exec_time' => 'Execution time',
        ];
    }
}
