<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User information
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            // Contact information
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
            ],

            'avatar' => [
                'sometimes',
                'nullable',
                'string',
                'max:2048',
            ],

            // Personal information
            'date_of_birth' => [
                'sometimes',
                'nullable',
                'date',
                'before:today',
            ],

            'gender' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
            ],

            // Address information
            'address_line_1' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'state' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'postal_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
            ],

            'country' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            // Additional information
            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ];
    }
}
