<?php

namespace App\Http\Requests;

use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'sale_date' => ['required', 'date'],
            'payment_status' => ['required', Rule::enum(PaymentStatus::class)],
            'customer_id' => ['nullable', 'required_if:payment_status,credit', 'exists:customers,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
