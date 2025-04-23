@if (isset($value))
    {{ number_format($value->value, 2, ',', '.') }} kz
@endif

@if (isset($credit_balance))
    {{ number_format($credit_balance->credit_balance, 2, ',', '.') }} kz
@endif

@if (isset($credit_balance_final))
    {{ number_format($credit_balance_final->credit_balance_final, 2, ',', '.') }} kz
@endif