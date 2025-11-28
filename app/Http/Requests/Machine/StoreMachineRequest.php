<?php

namespace App\Http\Requests\Machine;

use Illuminate\Foundation\Http\FormRequest;
class StoreMachineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'                  => ['required', 'string', 'max:50', 'unique:machines,code'],
            'name'                  => ['required', 'string', 'max:255'],
            'process_id'            => ['required', 'integer', 'exists:processes,id'],
            'speed_mh'              => ['nullable', 'numeric'],
            'speed_kgh'             => ['nullable', 'numeric'],
            'circumference_total'   => ['nullable', 'numeric'],
            'max_width'             => ['nullable', 'numeric'],
            'max_center'            => ['nullable', 'numeric'],
            'status'                => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The machine code is required.',
            'code.unique' => 'A machine with this code already exists.',
            'name.required' => 'The machine name is required.',
            'process_id.required' => 'The process is required.',
            'process_id.exists' => 'The selected process does not exist.',
        ];
    }
}
