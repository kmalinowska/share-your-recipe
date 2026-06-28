<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateRecipeTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],

            'category_id' => [
                'required',
                'exists:categories,id'
            ],

            'ingredients' => [
                'required',
                'array',
                'min:1'
            ],

            'ingredients.*.name' => [
                'required',
                'string',
                'max:255'
            ],

            'steps' => [
                'required',
                'array',
                'min:1'
            ],

            'steps.*' => [
                'required',
                'string'
            ]
        ];
    }
}
