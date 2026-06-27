<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_number.required' => 'Nomor resi wajib diisi.',
        ];
    }
}
