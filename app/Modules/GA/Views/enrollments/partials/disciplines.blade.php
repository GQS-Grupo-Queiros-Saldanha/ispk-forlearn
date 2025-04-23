<h3>{{ $study_plan_edition->currentTranslation->display_name }}</h3>
@php
    $years = $study_plan_edition->disciplines->groupBy(['year']);
@endphp
@if(count($years) > 0)
    @foreach($years as $year=>$disciplines)
        <h4>Ano {{ $year }}</h4>
        @if(count($disciplines) > 0)
            @foreach($disciplines as $discipline)
                {{ Form::bsCheckbox('disciplines[]', $discipline->discipline->id, null, [/*'disabled' => $action === 'show'*/], ['label' => $discipline->discipline->currentTranslation->display_name]) }}
{{--                <li>{{ $discipline->discipline->currentTranslation->display_name }}</li>--}}
            @endforeach
        @else
            Nenhum disciplina encontrada para este ano
        @endif
    @endforeach
@else
    Nenhum ano encontrado
@endif
