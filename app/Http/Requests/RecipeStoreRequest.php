<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RecipeStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'preparation_time' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'uuid', 'exists:categories,id'],

            // Photo validation (maximum 4MB, acceptable formats: jpg, jpeg, png, webp)
            'image_path' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],

            // Validation new step table with Alpine.js
            'steps'=> ['required', 'array', 'min:1'],
            'steps.*' => ['required', 'string'],

            // Validation of optional tags
            'tags' => ['nullable', 'array'],
            'tags.*' => ['uuid', 'exists:tags,id'],

            // Dynamic Component Array Validation
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.quantity' => ['required', 'string', 'max:50'],
            'ingredients.*.unit' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The recipe name field is required.',
            'preparation_time.required' => 'The time field is required.',
            'category_id.required' => 'Please select a valid category.',
            'steps.required' => 'You must add at least one preparation step.',
            'steps.*.required' => 'Each preparation step details cannot be empty',
            'ingredients.required' => 'You must add at least one ingredient.',
            'ingredients.*.name.required' => 'Ingredient name is required.',
            'ingredients.*.quantity.required' => 'Quantity is required.',
            'ingredients.*.unit.required' => 'Unit is required.',
            'image_path.image' => 'The uploaded file must be an image.',
            'image_path.mimes' => 'The image field must be a file of type: jpeg, png, jpg, webp.',
            'image_path.max' => 'The image size cannot exceed 4MB.',
        ];
    }
}
