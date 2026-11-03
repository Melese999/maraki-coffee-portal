<?php

namespace App\Http\Requests;

use App\Enums\InvestmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvestmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('investments.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'investor_id' => ['required', 'exists:investors,id'],
            'type' => ['required', Rule::enum(InvestmentType::class)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'invested_on' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
