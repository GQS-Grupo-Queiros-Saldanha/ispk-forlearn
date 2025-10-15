@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title', 'Calendária Formulário')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidates.index') }}">Candidaturas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidate.list_candidatura') }}">Calendário</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        @isset($action)
            @if ($action == 'PUT')
                Editar
            @else
                Visualizar
            @endif
        @else
            Criar
        @endisset
    </li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm"
            @if (isset($ano_candidatura->id)) disabled @endif>
            <option selected value="">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}"
                    @if (isset($ano_candidatura->id) && $ano_candidatura->id_years == $lectiveYear->id) selected  @elseif($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    @isset($action)
        <form action="{{ route('candidate.edit_store_candidatura', $id) }}" method="POST" id="FormaExcel">
            @method('PUT')
    @else
        <form action="{{ route('candidate.anoLectivoStore') }}" method="POST" id="FormaExcel">
    @endisset
        @csrf
            <input type="hidden" name="lective_year" id="lective_year_aux"
                @if (isset($ano_candidatura->id)) value="{{ $ano_candidatura->id }}" @endif />
            <div class="row pb-4">
                <div class="col-md-6">
                    <label for="data_inicio">
                        <i class="fas fa-calendar"></i>
                        <span>Data inicio</span>
                    </label>
                    <input class="form-control rounded" type="date" name="data_start" required
                        value="{{ isset($ano_candidatura->id) ? $ano_candidatura->data_inicio : $ano_lectivo->start_date }}"
                        id="date-start" min="{{ $ano_lectivo->start_date }}"
                        {{ isset($action) ? ($action != 'PUT' ? 'disabled' : '') : '' }}>
                </div>
                <div class="col-md-6">
                    <label for="data_inicio">
                        <i class="fas fa-calendar-check"></i>
                        <span>Data termino</span>
                    </label>
                    <input class="form-control rounded" type="date" name="data_end" required
                        value="{{ isset($ano_candidatura->id) ? $ano_candidatura->data_fim : $ano_lectivo->end_date }}"
                        id="date-end" max="{{ $ano_lectivo->end_date }}"
                        {{ isset($action) ? ($action != 'PUT' ? 'disabled' : '') : '' }}>
                </div>
            </div>
            <div class="w-100 position-relative">
                @isset($action)
                    <button type="{{ $action == 'PUT' ? 'submit' : 'button' }}" id="btn-criate"
                        class="btn  {{ $action == 'PUT' ? 'btn-primary' : '' }} rounded float-right-abs">
                        @if ($action == 'PUT')
                            <i class="fas fa-edit"></i>
                        @endif
                        <span>{{ $action == 'PUT' ? 'actualizar' : '' }}</span>
                    </button>
                @else
                    <button type="submit" class="btn btn-primary rounded float-right-abs" id="btn-criate">
                        <i class="fas fa-plus"></i>
                        <span>criar</span>
                    </button>
                @endisset
            </div>
        </form>
@endsection
@section('scripts-new')
    <script>
        (() => {
            const btnDataSubmit = $('#btn-criate');
            const dateStart = $('#date-start');
            const dateEnd = $('#date-end');
            const lective = $("#lective_year");
            const pageLective = $('#page-lective');

            $('#lective_year_aux').val(lective.val());

            lective.change(function() {
                if (lective.val() != "") {
                    $('#lective_year_aux').val(lective.val());
                    $.ajax({
                        url: "/users/candidates/validation_ano/" + lective.val(),
                        type: 'GET',
                        success: function(data) {
                            if (data.status == 1) {
                                let link = $('#candidates-create');
                                pageLective.html("Ano lectivo: " + data.body.start_date + " / " + data.body.end_date);
                                    if (data.body.is_termina == 1) {
                                        dateStart.val(data.body.start_date).attr('min', data.body
                                            .start_date).prop('disabled', true);
                                        dateEnd.val(data.body.end_date).attr('max', data.body.end_date)
                                            .prop('disabled', true);
                                        btnDataSubmit.addClass('d-none');
                                        warning("O ano lectivo se encontra fechado");
                                    } else {
                                        dateStart.val(data.body.start_date).attr('min', data.body
                                            .start_date).prop('disabled', false);
                                        dateEnd.val(data.body.end_date).attr('max', data.body.end_date)
                                            .prop('disabled', false);
                                        btnDataSubmit.removeClass('d-none');
                                    }
                                }
                            }
                        });
                    }
                });

            })();
    </script>
@endsection
