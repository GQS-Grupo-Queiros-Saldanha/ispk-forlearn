<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Análise estatístico do aproveitamento')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Análise estatístico do aproveitamento</li>
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
    {!! Form::open(['route' => ['generate_estatistic'], 'id' => 'estatistica_id', 'target' => '_blank']) !!}
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
                id="Curso_id_Select" data-actions-box="true" data-selected-text-format="values" name="id_curso[]"
                tabindex="-98" multiple>
                @foreach ($courses as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->currentTranslation['display_name'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione ano(s) curricular(es)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="AnoCurricular_id_Select" data-actions-box="true" data-selected-text-format="values"
                name="AnoCurricular_id[]" tabindex="-98" multiple></select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione a(s) discipina(s)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm"
                id="Disciplina_id_Select" data-actions-box="true" data-selected-text-format="values" name="id_disciplina[]"
                tabindex="-98" multiple></select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione a(s) tuma(s)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" id="Turma_id_Select"
                data-actions-box="true" data-selected-text-format="values" name="id_turma[]" tabindex="-98" multiple>
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione a(s) escala(s) de valor(es) de:</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" id="Escala_id_Select"
                data-actions-box="true" data-selected-text-format="values" name="id_escala_avaliacao[]" tabindex="-98"
                multiple>
                <option value="first">0 à 6 </option>
                <option value="second">7 à 9 </option>
                <option value="thirst">10 à 13 </option>
                <option value="fourth">14 à 16 </option>
                <option value="fiveth">17 à 19 </option>
                <option value="sixth">20</option>
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione à pauta:</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" id="Escala_id_Select"
                data-actions-box="false" data-selected-text-format="values" name="pauta_type" tabindex="-98">
                @forelse ($Pautas as $item)
                    <option value="{{ $item->PautaCode }},{{ $item->NamePauta }}">
                        {{ $item->NamePauta }}
                    </option>
                @empty
                    Sem nenhuma estatística no sistema!
                @endforelse
            </select>
        </div>
    </div>

    <input type="hidden" value="0" id="verificarSelector">
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="">
    <input type="hidden" id="data_html" name="data_html" value="">

    <div class="card mr-1" id="pauta_disciplina">
        <h4 id="titulo_semestre"></h4>
        <table class="table_pauta table-hover dark">
            <thead class="table_pauta">
                <tr id="listaMenu" style="text-align:center"></tr>
            </thead>
            <tbody id="lista_tr"></tbody>
        </table>
    </div>

    <div class="card mb-2" style="float: right;">
        <button type="button" class="btn btn-success" id="gerarBtn">
            <i class="fas fa-file-pdf" id="icone_publish"></i>
            Imprimir
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
                var escala = $("#Escala_id_Select").val();

                if (curso != "" && escala != "") {
                    $("#id_anoLectivo").val($("#lective_year").val());
                    $("#estatistica_id").submit();
                } else {
                    alert("Atenção! Os selectores curso, e escala de valores e pauta, devem estar selecionado.")
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
                AnoCurricular(id_cursos)
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
                    url: "/avaliations/PegarDisciplinaAnoCurricular/" + id_cursos + "/" + anoCurricular + "/" + id_anoLectivo.val(),
                    type: "GET",
                    data: {_token: '{{ csrf_token() }}' },
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

                    if (data["Turmas"].length) {
                        $.each(data["Turmas"], function(indexInArray, item) {
                            Turma_id_Select.append('<option value="' + item['id'] + '">' + item['display_name'] + '</option>');
                        });
                        Turma_id_Select.prop('disabled', false);
                        Turma_id_Select.selectpicker('refresh');
                    }
                });
            }

            function AnoCurricular(id_cursos) {
                $.ajax({
                    url: "/avaliations/PegarAnoCurricular/" + id_cursos,
                    type: "GET",
                    data: { _token: '{{ csrf_token() }}'},
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
                    AnoCurricular_id_Select.empty();
                    if (data > 0) {
                        for (var i = 1; i <= data; i++)
                            AnoCurricular_id_Select.append('<option value="' + i + '">' +i + '</option>');
                    } else {
                        AnoCurricular_id_Select.empty();
                        AnoCurricular_id_Select.prop('disabled', true)
                        AnoCurricular_id_Select.selectpicker('refresh');
                    }
                    AnoCurricular_id_Select.prop('disabled', false);
                    AnoCurricular_id_Select.selectpicker('refresh');
                    Turma_id_Select.empty();
                    Turma_id_Select.prop('disabled', false);
                });
            }
        })
    </script>
@endsection
