@php
$contador = 0;
@endphp


@foreach ($budget_articles as $artigos)
    @if ($item->id == $artigos->chapter_id)
        @php
            $contador++;
        @endphp
    @endif
@endforeach


{{ $contador}}