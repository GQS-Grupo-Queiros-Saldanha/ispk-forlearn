<title>Horários | forLEARN® by GQS</title>
@php
// Se não for um estudante
    $isNotStudent = !auth()
        ->user()
        ->hasRole(['student']);
    $isTeacher = auth()
        ->user()
        ->hasRole(['teacher']);
@endphp
@extends('layouts.generic_index_new')
@section('page-title', 'Horários')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Horários</li>
@endsection
@section('selects')
{{-- Se não for um estudante --}}
    @if ($isNotStudent)
        <div>
            <label for="lective_year">Selecione o ano lectivo</label>
            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
                @foreach ($lectiveYears as $lectiveYear)
                    <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <h1>Infelismente o estudante não têm uma matrícula neste ano lectivo, por está razão não é possivél exibir o
            horário.</h1>
    @endif
@endsection
@section('body')
{{-- Se não for um estudante --}}
    @if($isNotStudent)
        <table id="schedules-table" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>@lang('common.code')</th>
                    <th>@lang('translations.display_name')</th>
                    <th>Turma</th>
                    <th>Turno</th>
                    <th>Semestre</th>
                    <th>Data de início</th>
                    <th>Data de fim</th>
                    <th>@lang('common.created_by')</th>
                    <th>@lang('common.updated_by')</th>
                    <th>@lang('common.created_at')</th>
                    <th>@lang('common.updated_at')</th>
                    <th>@lang('common.actions')</th>
                </tr>
            </thead>
        </table>
    @endif
@endsection
@section('models')
    @parent
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    {{-- Se não for um estudante --}}
    @if ($isNotStudent)
            <script>
                $(document).ready(function() {
                    var lective_year = $("#lective_year").val();

                    $("#lective_year").change(function() {
                        lective_year = $("#lective_year").val();
                        $("#schedules-table").DataTable().destroy();
                        get_schedule(lective_year);
                    });

                    get_schedule(lective_year);

                    function get_schedule(id_lective_year) {
                        $('#schedules-table').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "ajax": {
                                url: "schedules_ajax/" + id_lective_year,
                                type: "GET",
                            },
                            "buttons": ['colvis','excel', 
                           
                            @if (auth()->user()->hasAnyPermission(['gerir_Novoshorarios']))
    {
        text: '<i class="fas fa-plus-square"></i>  @lang("common.new")',
        className: 'btn-primary main ml-1 rounded btn-main new_matricula',
        action: function(e, dt, node, config) {
            window.open("{{ route('schedules.create') }}");
        }
    }
    @endif
                ],
                       
                            "columns": [{
                                    data: 'code',
                                    name: 'code',
                                    visible: false
                                },
                                {
                                    data: 'display_name',
                                    name: 'st.display_name'
                                },
                                {
                                    data: 'cl_turma',
                                    name: 'cl_turma'
                                },
                                {
                                    data: 'turno',
                                    name: 'turno'
                                },
                                {
                                    data: 'semestre',
                                    name: 'semestre'
                                },
                                {
                                    data: 'start_at',
                                    name: 'schedules.start_at',
                                    visible: false
                                },
                                {
                                    data: 'end_at',
                                    name: 'schedules.end_at',
                                    visible: false
                                },
                                {
                                    data: 'created_by',
                                    name: 'created_by',
                                    visible: false
                                },
                                {
                                    data: 'updated_by',
                                    name: 'updated_by',
                                    visible: false
                                },
                                {
                                    data: 'created_at',
                                    name: 'created_at',
                                    visible: false
                                },
                                {
                                    data: 'updated_at',
                                    name: 'updated_at',
                                    visible: false
                                },
                                {
                                    data: 'actions',
                                    name: 'actions',
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            "lengthMenu": [
                                [10, 25, 100, -1],
                                [10, 25, 100, "Todos"]
                            ],

                        });
                    }
                });
                // Delete confirmation modal
                Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
            </script>
        @endif

@endsection
