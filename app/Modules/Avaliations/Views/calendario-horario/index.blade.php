<title>Avaliações | forLEARN® by GQS</title>
@php $isPermission = auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas']); @endphp
@extends('layouts.generic_index_new')
@section('page-title', 'Calendário de agendamento de prova')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Calendário de agendamento de prova</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected->id == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <table id="calendarie-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Regime</th>
                <th>Intervalo</th>
                <th>Curso</th>
                <th>Turma</th>
                <th>Disciplina</th>
                <th>Data marcada</th>
                <th>Hora começo</th>
                <th>Hora termico</th>
                <th>Periodo</th>
                <th>Ano curricular</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('Avaliations::calendario-horario.modal.juris')
    @include('Avaliations::calendario-horario.modal.delete_prova')
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            const calendarieTable = $('#calendarie-table');

            function buttonsGroup() {
                let data = ['colvis', 'excel'];
                @if($isPermission)
                    data.push({
                        text: '<i class="fas fa-plus-square"></i>  Criar novo agendamento',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            window.open('{!! route('calendario_prova_horario.create') !!}', "_blank");
                        }
                    });
                    data.push({
                        text: '<i class="fas fa-search"></i>  Procura provas',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            window.open('{!! route('calendario_prova_horario.search') !!}', "_blank");
                        }
                    });
                @endif
                return data;
            }

            function ajaxCalendarioProvaHorario() {
                calendarieTable.DataTable({
                    ajax: '{!! route('ajax.calendario_horario') !!}',
                    buttons: buttonsGroup(),
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        }, {
                            data: 'regime',
                            name: 'regime',
                            searchable: true
                        }, {
                            data: 'intervalo',
                            name: 'intervalo',
                            searchable: true
                        }, {
                            data: 'curso',
                            name: 'curso',
                            searchable: true
                        }, {
                            data: 'turma',
                            name: 'turma',
                            searchable: true
                        }, {
                            data: 'disciplina',
                            name: 'disciplina',
                            searchable: true
                        }, {
                            data: 'data_marcada',
                            name: 'data_marcada',
                            searchable: true
                        }, {
                            data: 'hora_comeco',
                            name: 'hora_comeco',
                            searchable: true
                        }, {
                            data: 'hora_termino',
                            name: 'hora_termino',
                            searchable: true
                        },
                        {
                            data: 'periodo',
                            name: 'periodo',
                            searchable: true
                        },
                        {
                            data: 'year',
                            name: 'year',
                            searchable: true
                        },
                        {
                            data: 'us_created_by',
                            name: 'us_created_by',
                            visible: false
                        },
                        {
                            data: 'us_updated_by',
                            name: 'us_updated_by',
                            visible: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            visible: false
                        }, {
                            data: 'updated_at',
                            name: 'updated_at',
                            visible: false
                        }, {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                    }
                });
            }
            ajaxCalendarioProvaHorario();
        })();
    </script>
@endsection
