@php
$contador = 0;
@endphp


@foreach ($budget_chapter as $capitulo)
    @if ($item->id == $capitulo->budget_id)
        @php
            $contador++;
        @endphp
    @endif
@endforeach


{{ $contador}}