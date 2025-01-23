<?php

namespace Ramatimati\Waliby\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventPostRequest extends FormRequest
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
            'eventname' => ['required', 'string'],
            'eventType' => ['required', 'string'],
            'messageTemplate' => ['required', 'string', 'exists:waliby_message_templates,id'],
            'receiverParams' => ['required', 'string'],
            'scheduledEvery' => ['required_if:eventType,recurring'],
            'scheduledAt' => ['required_if:eventType,recurring', 'max:28']
        ];
    }

    public function message(): array
    {
        return [
            'eventname.required' => 'Event name is required',
            'eventname.string' => 'Event name must be a string',
            'eventType.required' => 'Event type is required',
            'eventType.string' => 'Event type must be a string',
            'messageTemplate.required' => 'Message template is required',
            'messageTemplate.string' => 'Message template must be a string',
            'messageTemplate.exists' => 'Message template not found',
            'receiverParams.required' => 'Receiver is required',
            'receiverParams.string' => 'Receiver must be a string',
            'scheduledEvery.required_if' => 'Scheduled Every is required',
            'scheduledAt.required_if' => 'Scheduled At is required',
            'scheduledAt.max' => 'Scheduled At must less or equal than 28',
        ];
    }
}
