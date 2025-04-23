


@php
$money = 0;
@endphp


@foreach ($budget_articles as $artigos)
    @if ($item->id == $artigos->chapter_id)
        @php
            $money = $money + ($artigos->money*$artigos->quantidade);
        @endphp
    @endif
@endforeach


{{ number_format($money, 2, ',', '.') . ' kz' }}