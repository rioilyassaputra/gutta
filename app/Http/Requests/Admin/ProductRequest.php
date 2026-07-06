<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'        => $this->name ? strip_tags($this->name) : null,
            'description' => $this->description ? strip_tags($this->description) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'                      => ['required', 'string', 'max:200'],
            'description'               => ['nullable', 'string'],
            'price'                     => ['required', 'numeric', 'min:0'],
            'weight_grams'              => ['required', 'integer', 'min:1'],
            'category_id'              => ['required', 'exists:categories,id'],
            'is_active'                 => ['boolean'],
            'images'                    => ['nullable', 'array'],
            'images.*'                  => ['file', 'mimes:jpeg,png,webp', 'max:5120'],
            'variants'                  => ['nullable', 'array'],
            'variants.*.size'           => ['required', 'string', 'max:10'],
            'variants.*.color'          => ['nullable', 'string', 'max:50'],
            'variants.*.sku'            => ['nullable', 'string', 'max:50', 'distinct'],
            'variants.*.stock'          => ['required', 'integer', 'min:0'],
            'variants.*.additional_price' => ['numeric', 'min:0'],
        ];
    }
}
