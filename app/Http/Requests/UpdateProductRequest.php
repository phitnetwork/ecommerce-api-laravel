<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'name'        => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'image'       => ['sometimes', 'string'], // o 'url'
            'tags'        => ['sometimes', 'array'],  // [] = stacca tutti i tag
            'tags.*'      => ['string'],
        ];
    }
}
