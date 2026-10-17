<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Guest checkout is allowed.
        return true;
    }

    public function rules(): array
    {
        $isOnline = $this->input('payment_method') === 'online';

        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],

            'delivery_address' => ['required', 'string', 'max:5000'],
            'district' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'customer_note' => ['nullable', 'string', 'max:2000'],

            'payment_method' => [
                'required',
                Rule::in(['cod', 'online']),
            ],

            'payment_provider' => [
                Rule::requiredIf($isOnline),
                Rule::prohibitedIf(! $isOnline),
                'nullable',
                Rule::in(['bkash', 'nagad']),
            ],

            'transaction_id' => [
                Rule::requiredIf($isOnline),
                Rule::prohibitedIf(! $isOnline),
                'nullable',
                'string',
                'max:255',
            ],

            'items' => ['required', 'array', 'min:1', 'max:100'],

            'items.*.product_variant_id' => [
                'required',
                'integer',
                'distinct',
                'exists:product_variants,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
        ];
    }
}