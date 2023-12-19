<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferActionFormRequest extends FormRequest
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
            'transfer.initiator_id' => ['required', 'numeric', 'exists:users,id'],
            'transfer.recipient_id' => ['required', 'numeric', 'different:transfer.initiator_id', 'exists:users,id'],
            'transfer.amount' => ['required', 'numeric', 'gt:0'],
            'transfer.exec_time' => ['nullable', 'numeric', 'min:0'],
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
            'transfer.initiator_id' => 'From user',
            'transfer.recipient_id' => 'To user',
            'transfer.amount' => 'Amount',
            'transfer.exec_time' => 'Execution time',
        ];
    }
}
