<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Análise estatístico do aproveitamento por disciplina')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Análise estatístico do aproveitamento por disciplina</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/new_switcher.css') }}">
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            @foreach ($lectiveYearsP as $lectiveYear)
                <option value="{{ $lectiveYear->Ano }}">
                    {{ $lectiveYear->Ano }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    {!! Form::open(['route' => ['generate_estatistic_geral'], 'id' => 'estatistica_id', 'target' => '_blank']) !!}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-6 p-2">
            <label>Selecione o(s) curso(s)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Curso_id_Select" data-actions-box="false" data-selected-text-format="values" name="id_curso"
                tabindex="-98">
                @foreach ($courses as $item)
                    <option value="{{ $item->id }}"> {{ $item->currentTranslation['display_name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione a(s) discipina(s)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm"
                id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="id_disciplina"
                tabindex="-98"></select>
        </div>
    </div>
    <input type="hidden" value="" id="verificarSelector" name="documento_set">
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="">
    <div class="card mr-1" id="pauta_disciplina">
        <h4 id="titulo_semestre"></h4>
        <table class=" table table_pauta table-hover dark">
            <thead class="table_pauta">
                <tr id="listaMenu" style="text-align:center"></tr>
            </thead>
            <tbody id="lista_tr"></tbody>
        </table>
    </div>
    <div class="card mb-2" style="float: right;margin-right:2px;">
        <button type="button" class="btn btn-success" id="gerarBtn">
            <i class="fas fa-file-pdf" id="icone_publish"></i>
            Estatística
        </button>
    </div>
    <div class="card mb-2" style="float: right;">
        <button type="button" class="btn btn-dark" id="gerarBtn_Estudant">
            <i class="fas fa-file-pdf" id="icone_publish"></i>
            Estudantes
        </button>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            //To generate
            $("#gerarBtn").click(function() {
                var curso = $("#Curso_id_Select").val();
                if (curso != "") {
                    $("#id_anoLectivo").val($("#lective_year").val());
                    $("#verificarSelector").val("");
                    $("#verificarSelector").val(1);
                    $("#estatistica_id").submit();
                } else {

                }
            });
            //Estudantes
            $("#gerarBtn_Estudant").click(function() {
                var curso = $("#Curso_id_Select").val();
                if (curso != "") {
                    $("#id_anoLectivo").val($("#lective_year").val());
                    $("#verificarSelector").val("");
                    $("#verificarSelector").val(0);
                    $("#estatistica_id").submit();
                } else {

                }
            });
            var id_anoLectivo = $("#lective_year");
            var Disciplina_Select = $("#Disciplina_id_Select");
            var Turma_id_Select = $("#Turma_id_Select");
            var AnoCurricular_id_Select = $("#AnoCurricular_id_Select");
            var Curso_id_Select = $("#Curso_id_Select");
            //Pegar nos cursos //E selecionar o ano curricular
            Curso_id_Select.change(function() {
                var id_cursos = Curso_id_Select.val();
                DisciplinaCurso(id_cursos)
            });
            //Pegar disciplinas 
            AnoCurricular_id_Select.change(function() {
                if (Curso_id_Select.val() != "") {
                    DisciplinaAnocurricular(Curso_id_Select.val(), AnoCurricular_id_Select.val())
                }
            });
            //Pega dsciplina e as turmas
            function DisciplinaAnocurricular(id_cursos, anoCurricular) {
                $.ajax({
                    url: "/avaliations/PegarDisciplinaAnoCurricular/" + id_cursos + "/" + anoCurricular +
                        "/" + id_anoLectivo.val(),
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if (id_cursos == "") {
                            return false;
                        }
                    }

                }).done(function(data) {
                    Disciplina_Select.empty();
                    Turma_id_Select.empty();
                    if (data["disciplina"].length) {
                        $.each(data["disciplina"], function(indexInArray, item) {
                            Disciplina_Select.append('<option value="' + item['id_disciplina'] +
                                '">' + item['code_disciplina'] + '-' + item['nome_disciplina'] +
                                '</option>');
                        });
                        Disciplina_Select.prop('disabled', false);
                        Disciplina_Select.selectpicker('refresh');
                    }

                });
            }

            function DisciplinaCurso(id_cursos) {
                $.ajax({
                    url: "/avaliations/PegarDisciplina/" + id_cursos,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if (id_cursos == "") {
                            AnoCurricular_id_Select.empty();
                            AnoCurricular_id_Select.prop('disabled', true);
                            return false;
                        }
                    }

                }).done(function(data) {
                    Disciplina_Select.empty();
                    if (data.length) {
                        $.each(data, function(indexInArray, item) {
                            Disciplina_Select.append('<option value="' + item['id_disciplina'] +
                                '">' + item['code_disciplina'] + '-' + item['nome_disciplina'] +
                                '</option>');
                        });
                        Disciplina_Select.prop('disabled', false);
                        Disciplina_Select.selectpicker('refresh');
                    }
                });

            }

        })
    </script>
@endsection
