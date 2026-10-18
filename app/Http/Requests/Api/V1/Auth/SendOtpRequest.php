<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Enums\VerificationPurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'purpose' => ['nullable', 'string', Rule::in(VerificationPurpose::values())],
        ];
    }
}
