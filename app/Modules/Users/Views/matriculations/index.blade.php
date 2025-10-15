<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Matrículas')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Matrículas</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-2">
        <label>Selecione o curso</label>
        <select name="curso" id="curso" class="selectpicker form-control form-control-sm"
            style="width: 100%!important;">
            <option selected value="">Seleciona o curso</option>
        </select>
    </div>
    <div class="">
        <label>Selecione o ano curricular</label>
        <select name="curso_years" id="curso_years" class="selectpicker form-control form-control-sm"
            style="width: 100%!important;">
            <option selected value="">Seleciona o ano curricular</option>
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
                <th>Sexo </th>
                <th>Contacto </th>
                <th>Escola de proveniência </th>
                <th>Data de nascimento </th>
                <th>Regime </th>
                <th>Categoria </th>
                <th>Instituição Bolseira </th>
                <th>Pagamento</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Atividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::anulate_matriculation.datatables.anulate')
    @include('Users::matriculations.model.modal_mudanca_curso')
@endsection
@section('scripts-new')
    <script>
        (() => {
            let curso = $("#curso");
            let curso_years = $("#curso_years");
            let id_curso = $("#curso");
            let id_anoLective = $("#lective_years");
            let table = $('#matriculations-table');
            let AnoDataTable;

            getCurso();
            ajaxMatricula('{!! route('matriculations.ajax') !!}');

            $('.new-finalist').attr('href', 'show-matriculation-listaFinalista')

            AnoDataTable.page('first').draw('page');
            Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

            if ($("#lective_years").val() > 6) {
                $("#group1").hide();
                $("#group2").show();
            } else {
                $("#group1").show();
                $("#group2").hide();
            }

            function ajaxMatricula(url) {
                let tam = table.children('tbody').length;
                if (tam > 0) table.DataTable().clear().destroy();
                AnoDataTable = table.DataTable({
                    ajax: url,
                    buttons: [
                        'colvis',
                        'excel', {
                            text: '<i class="fas fa-plus-square"></i> Criar nova confirmação de matrícula',
                            className: 'btn-primary main ml-1 rounded btn-main new_matricula',
                            action: function(e, dt, node, config) {
                                id_anoLectivC = $("#lective_years").val();
                                let url = 'confirmation_matriculation/create/' + id_anoLectivC;
                                window.open(url, "_blank");
                            }
                        }, {
                            text: '<i class="fas fa-plus-square"></i> Criar nova confirmação de matrícula | equivalência',
                            className: 'btn-warning main ml-1 rounded btn-main new_matricula',
                            action: function(e, dt, node, config) {
                                id_anoLectivC = $("#lective_years").val();
                                let url = 'confirmations-equivalence/' + id_anoLectivC;
                                window.open(url, "_blank");
                            }
                        }, {
                            text: '<i class="fas fa-user"></i> Estados dos estudantes',
                            className: 'btn-primary main ml-1 rounded btn-main',
                            action: function(e, dt, node, config) {
                                window.open('/users/states-matriculation', "_blank");
                            }
                        },
                        {
                            text: '<i class="fas fa-plus"></i> Matrícula finalista',
                            className: 'btn-primary main ml-1 rounded btn-main',
                            action: function(e, dt, node, config) {
                                id_anoLective = $("#lective_years").val();
                                window.open('/users/show-matriculation-listaFinalista', "_blank");
                            }
                        }
                        @if (auth()->user()->hasAnyRole(['superadmin', 'staff_matriculas']))
                            ,

                            {
                                text: 'Gerar registro primário de matriculados',
                                className: 'btn-primary main ml-1 rounded btn-text',
                                action: function(e, dt, node, config) {
                                    year = $("#lective_years").val();
                                    let path = 'excel-matriculados/' + year;
                                    window.open(path, "_self");

                                }
                            }
                        @endif
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false

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

                            data: 'sexo',
                            name: 'po_sexo.code',
                            visible: false

                        },
                        {

                            data: 'contacto',
                            name: 'up_contact.value',
                            visible: false

                        },
                        {

                            data: 'escola',
                            name: 'up_escola.value',
                            visible: false

                        },
                        {

                            data: 'data_nascimento',
                            name: 'up_data_nascimento.value',
                            visible: false

                        },
                        {
                            data: 'regime',
                            name: 'rre.nome',
                            visible: false

                        },
                        {

                            data: 'categoria',
                            name: 'ent.type',
                            visible: false

                        },
                        {

                            data: 'entidade',
                            name: 'ent.company',
                            visible: false
                        },
                        {
                            data: 'states',
                            name: 'state',
                            searchable: false
                        }, {
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
            }

            function getCurso() {
                $.ajax({

                    url: "getCurso",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                }).done(function(data) {

                    curso.empty();
                    curso.append('<option selected="" value="0">Selecione o curso</option>');

                    if (data['data'].length > 0) {
                        $.each(data['data'], function(indexInArray, row) {
                            curso.append('<option value="' + row.id + '">' + row.nome_curso +
                                '</option>');
                        });
                    }

                    curso.prop('disabled', false);
                    curso.selectpicker('refresh');

                });
            }

            function getCursoAno(id_curso) {

                id_anoLective = $("#lective_years").val();

                $.ajax({

                    url: "getCursoAno/" + id_curso + "/" + id_anoLective,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                }).done(function(data) {

                    curso_years.empty();
                    curso_years.append('<option selected="" value="0">Selecione o ano curricular</option>');

                    if (data['data'].length > 0) {
                        $.each(data['data'], function(indexInArray, row) {
                            curso_years.append('<option value="' + row.course_year + '">' + row
                                .course_year +
                                '</option>');
                        });
                    }

                    curso_years.prop('disabled', false);
                    curso_years.selectpicker('refresh');

                });
            }

            id_anoLective.bind('change keypress', function() {
                id_anoLective = $("#lective_years").val();
                $('.new-finalist').attr('href', 'show-matriculation-listaFinalista')
            });

            $(".new_matricula").click(function(e) {
                id_anoLectivC = $("#lective_years").val();
                $(this).attr('href', 'confirmation_matriculation/create/' + id_anoLectivC);
            });

            $("#lective_years").change(function() {

                var lective_years = $("#lective_years").val();
                getCurso();
                // getCursoAno(); 
                curso_years.empty();
                curso_years.append('<option selected="" value="0">Selecione o ano curricular</option>');
                curso_years.prop('disabled', false);
                curso_years.prop('disabled', false);
                curso_years.selectpicker('refresh');
                console.log(lective_years);

                if (lective_years > 6) {
                    $("#group1").hide();
                    $("#group2").show();
                } else {
                    $("#group1").show();
                    $("#group2").hide();
                }

                if (lective_years <= 6) {
                    $("#add-pa").prop('hidden', false);
                } else {
                    $("#add-pa").prop('hidden', true);
                }

                ajaxMatricula("/users/get_matriculation_list_by/" + lective_years);
                
            })

            curso.bind('change keypress', function() {
                var id_curso = curso.val();
                getCursoAno(curso.val());

                id_anoLective = $("#lective_years").val();
                ajaxMatricula(`getMatriculasCourse/${id_curso}/${id_anoLective}`);

                AnoDataTable.page('first').draw('page');
                Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
            })

            curso_years.bind('change keypress', function() {
                var id_curso_years = curso_years.val();
                id_anoLective = $("#lective_years").val();

                ajaxMatricula(`getMatriculasCourseAno/${curso.val()}/${id_curso_years}/${id_anoLective}`)

                AnoDataTable.page('first').draw('page');
                Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
            })
        })();
    </script>
@endsection
