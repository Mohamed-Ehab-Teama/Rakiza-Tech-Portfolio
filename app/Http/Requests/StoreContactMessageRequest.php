<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for the website's contact form (anyone may send it).
 */
class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'website' => ['nullable', 'string', 'max:255'],   // honeypot — must stay empty
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name is required',
            'email.required' => 'a valid email is required',
            'email.email' => 'a valid email is required',
            'message.required' => "message can't be empty",
            'message.min' => 'message is too short',
        ];
    }

    /** Without JavaScript, show the errors at the contact section, not the top of the page. */
    protected function getRedirectUrl(): string
    {
        return route('home').'#contact';
    }

    /** Spam bots fill in every field, including the hidden "website" one. */
    public function isSpam(): bool
    {
        return filled($this->input('website'));
    }
}
