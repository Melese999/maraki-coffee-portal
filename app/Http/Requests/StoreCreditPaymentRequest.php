<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('credit.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'sale_id' => ['required', 'exists:sales,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
