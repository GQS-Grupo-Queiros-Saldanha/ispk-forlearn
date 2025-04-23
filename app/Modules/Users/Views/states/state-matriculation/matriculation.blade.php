@extends('layouts.generic_index_new')
@section('page-title', 'Estado dos Matriculados')
@section('styles-new')
    @parent
    <style>
        .dt-buttons .buttons-html5 {
            border-top-right-radius: 0px !important;
            border-bottom-right-radius: 0px !important;
        }

        .dt-buttons .buttons-pdf {
            border-top-right-radius: 6px !important;
            border-bottom-right-radius: 6px !important;
        }
    </style>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Estado dos Matriculados</li>
@endsection
@section('selects')
    <div class="">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm"
            style="width: 100% !important;">
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <table id="matriculations-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th id="dado">#</th>
                <th>Confirmação </th>
                <th>Matrícula</th>
                <th>Nome do estudante</th>
                <th>E-mail</th>
                <th>Curso</th>
                <th>Ano </th>
                <th>Turma </th>
                <th>nº BI </th>
                <th>Estado do estudante</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Atividades</th>

            </tr>
        </thead>
    </table>
    <div hidden>
        <a href="{{ route('student_state.create') }}" id="novo-estado"></a>
    </div>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::states.state-matriculation.modal')
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script>

            let lective_years = $("#lective_years");
            let table = $('#matriculations-table');
            let AnoDataTable;

            function ajaxEstadoMatricula(url) {
                let tam = table.children('tbody').length;
                if (tam > 0) table.DataTable().clear().destroy();

                AnoDataTable = table.DataTable({
                    ajax: url,
                    buttons: ['colvis', 'copy','print', 'csv', 'excel', 'pdf', {
                        text: '<i class="fas fa-plus-square"></i> Novo',
                        className: 'btn-primary main ml-1 rounded',
                        action: function(e, dt, node, config) {
                            window.open($('#novo-estado').attr('href'), "_blank");
                        }
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                        }, {
                            data: 'code_matricula',
                            name: 'matriculations.code'
                        }, {
                            data: 'matricula',
                            name: 'up_meca.value'
                        }, {
                            data: 'student',
                            name: 'u_p.value'
                        }, {
                            data: 'email',
                            name: 'u0.email',
                            visible: false
                        }, {
                            data: 'course',
                            name: 'ct.display_name'
                        }, {
                            data: 'course_year',
                            name: 'course_year'
                        }, {
                            data: 'classe',
                            name: 'cl.display_name'
                        }, {

                            data: 'n_bi',
                            name: 'up_bi.value'

                        },

                        {
                            data: 'student_states',
                            name: 'states_studant.name',
                            searchable: false
                        },

                        {
                            data: 'criado_por',
                            name: 'u1.name',
                            visible: false
                        }, {
                            data: 'actualizado_por',
                            name: 'u2.name',
                            visible: false
                        }, {
                            data: 'created_at',
                            name: 'created_at',
                            visible: false
                        }, {
                            data: 'updated_at',
                            name: 'updated_at',
                            visible: false
                        }, {
                            data: 'actions',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    "lengthMenu": [
                        [10, 100, 50000],
                        [10, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });

                if ($("#lective_years").val() > 6) {
                    $("#group1").hide();
                    $("#group2").show();
                } else {
                    $("#group1").show();
                    $("#group2").hide();
                }
            }

            function historic_modal(element) {
                let id = element.getAttribute("data-id");
                let now_state = element.getAttribute("data-state");
                now_state != "" ? now_state : "Sem estado associado";
                let full_name = element.getAttribute("data-name");
                $("#GerarPdf_estado").attr('href', '');
                $("#GerarPdf_estado").attr('href', 'pdf-states_historic/' + id);
                console.log(id + "--" + now_state);

                $("#staticBackdropLabel").html(full_name + "<br>Estado actual: " + now_state +
                    " <i class='fa fa-check' aria-hidden='true'></i>");
                $("#actual_state").text("Estado actual:");
                $("#historicStateModal").modal("show");

                $("#states-table").DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    buttons: [
                        'colvis',
                        'excel'
                    ],
                    ajax: "states_historic_ajax/" + id,
                    columns: [{
                            data: 'initials',
                            name: 'initials'
                        },
                        {
                            data: 'user_name',
                            name: 'user_name'
                        },
                        {
                            data: 'student_states',
                            name: 'student_states'
                        },
                        {
                            data: 'state_type',
                            name: 'state_type'
                        },
                        {
                            data: 'occurred_at',
                            name: 'occurred_at'
                        },
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });
            }

            ajaxEstadoMatricula('state_matriculations_ajax/' + lective_years.val());

            $("#lective_years").change(function() {
                ajaxEstadoMatricula('state_matriculations_ajax/' + lective_years.val());
            });

            $("#close_modal").click(function() {
                $("#historicStateModal").modal("hide");
            });

            Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    
    </script>
@endsection
