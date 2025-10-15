@component('mail::message')
# Referência para pagamento

@component('mail::table')
    |   |   |
    |--:|:--|
    | Utilizador:&nbsp; | **{{ $userName }}** |
    | Emolumento / Propina:&nbsp; | **{{ $articleName }}** |
    | Entidade:&nbsp; | **99915** |
    | Referência:&nbsp; | **{{ $transactionUid }}** |
    | Montante:&nbsp;   | **{{ $totalValue }}** |
@endcomponent

Com os melhores cumprimentos,<br>
{{ config('app.name') }}
@endcomponent
