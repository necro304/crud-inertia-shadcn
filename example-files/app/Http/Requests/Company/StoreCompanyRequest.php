<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Implement authorization logic with policies
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:255', 'unique:companies,nit'],
            'logo' => ['nullable', 'image', 'max:2048'], // Max 2MB
            'active' => ['sometimes', 'in:0,1,true,false'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert "0"/"1" strings to boolean
        if ($this->has('active')) {
            $this->merge([
                'active' => filter_var($this->active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'company name',
            'legal_name' => 'legal name',
            'nit' => 'tax identification number',
            'logo' => 'company logo',
            'active' => 'active status',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The company name is required.',
            'nit.required' => 'The tax identification number (NIT) is required.',
            'nit.unique' => 'This NIT is already registered.',
            'logo.image' => 'The logo must be an image file.',
            'logo.max' => 'The logo size must not exceed 2MB.',
        ];
    }
}
