<?php

namespace App\Enums;

enum InvestmentType: string
{
    case Loan = 'loan';
    case Equity = 'equity';

    public function label(): string
    {
        return match ($this) {
            self::Loan => __('Loan'),
            self::Equity => __('Equity'),
        };
    }
}
