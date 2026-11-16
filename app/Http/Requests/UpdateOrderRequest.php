<?php

namespace App\Http\Requests;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('orders.update') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::enum(OrderStatus::class)],
            'payment_status' => ['sometimes', Rule::enum(OrderPaymentStatus::class)],
            'convert_to_sale' => ['sometimes', 'boolean'],
        ];
    }
}
