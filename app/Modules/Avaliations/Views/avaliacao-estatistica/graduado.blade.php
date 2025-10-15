<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estatística avaliação')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Análise estatístico</li>
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
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    {!! Form::open(['route' => ['generate_estatistic.graduado'], 'id' => 'estatistica_id', 'target' => '_blank']) !!}
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
                    <option value="{{ $item->id }}"> {{ $item->currentTranslation['display_name'] }} </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-6 p-2">
        <p for="" id="mensagem_retorno"></p>
    </div>
    <input type="hidden" value="" id="verificarSelector" name="documento_set">
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="">
    <input type="hidden" id="id_disciplina" name="id_disciplina" value="" required>
    <div class="card mr-1" id="pauta_disciplina">
        <h4 id="titulo_semestre"></h4>
        <table class="table table_pauta table-hover dark">
            <thead class="table_pauta">
                <tr id="listaMenu" style="text-align:center"></tr>
            </thead>
            <tbody id="lista_tr"></tbody>
        </table>
    </div>
    <div class="card mb-2" style="float: right;margin-right:2px;">
        <button type="button" class="btn btn-success" id="gerarBtn">
            <i class="fas fa-file-pdf" id="icone_publish"></i>
            Estatística dos graduados
        </button>
    </div>
    <div class="card mb-2" style="float: right;">
        <button type="button" class="btn btn-dark" id="gerarBtn_Estudant">
            <i class="fas fa-file-pdf" id="icone_publish"></i>
            Listar graduados
        </button>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script>
        $(document).ready(function() {
            //To generate
            $("#gerarBtn").click(function() {
                var curso = $("#Curso_id_Select").val();
                if (curso != "") {
                    $("#id_anoLectivo").val($("#lective_year").val());

                    $("#verificarSelector").val("");
                    $("#verificarSelector").val(2);
                    $("#estatistica_id").submit();
                } else {
                    alert("Selecione um curso antes de tentar imprimir.")
                }
            });
            //Estudantes
            $("#gerarBtn_Estudant").click(function() {
                var curso = $("#Curso_id_Select").val();
                if (curso != "") {
                    $("#id_anoLectivo").val($("#lective_year").val());
                    $("#verificarSelector").val("");
                    $("#verificarSelector").val(3);
                    $("#estatistica_id").submit();
                } else {
                    alert("Selecione um curso antes de tentar imprimir.")
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
            id_anoLectivo.change(function() {
                DisciplinaCurso(Curso_id_Select.val())
            });
            //Pega dsciplina e as turmas
            function DisciplinaCurso(id_cursos) {
                var anoL = $("#lective_year").val();
                $.ajax({
                    url: "/avaliations/PegarDisciplinasAnoCurricularGraduado/" + id_cursos + "/" + anoL,
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
                    if (JSON.stringify(data) === '{}') {
                        //Vazio o resultado
                        $("#id_disciplina").val("");
                        alert("Não foi encontrado nenhuma edição de plano de estudo no ano lectivo selecionado para que lhe possa ser mostrado os graduados.Por favor verifica se selecionou o ano lectivo correcto.");
                    } else {
                        $("#id_disciplina").val(data.id_disciplina);
                    }
                });
            }
        })
    </script>
@endsection
