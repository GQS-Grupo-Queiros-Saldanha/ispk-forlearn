@php
$money = 0;
@endphp


@foreach ($budget_articles as $capitulo)
    @if ($item->id == $capitulo->budget_id)
        @php
             $money = $money + ($capitulo->money*$capitulo->quantidade);
        @endphp
    @endif
@endforeach


{{ number_format($money, 2, ',', '.') . ' kz' }}