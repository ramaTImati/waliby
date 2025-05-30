<?php

namespace Ramatimati\Waliby\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplatePostRequest extends FormRequest
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
            'name' => ['required', 'string', 'unique:waliby_message_templates'],
            'template' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.unique' => 'Name must be unique',
            'template.required' => 'Template is required',
            'template.string' => 'Template must be a string',
        ];
    }
}
