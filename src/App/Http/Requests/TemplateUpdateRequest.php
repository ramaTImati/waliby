<?php

namespace Ramatimati\Waliby\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateUpdateRequest extends FormRequest
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
            'templateId' => ['required', 'string'],
            'text' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'templateId.required' => 'Template ID is required, please refresh the page and try again',
            'templateId.string' => 'Template ID must be a string',
            'text.required' => 'Template is required',
            'text.string' => 'Template must be a string',
        ];
    }
}
