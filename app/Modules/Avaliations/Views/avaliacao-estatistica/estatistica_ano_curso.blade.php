<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Análise estatístico anual')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Análise estatístico anual</li>
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
    {!! Form::open(['route' => ['generate_estatistic_anual'], 'id' => 'estatistica_id', 'target' => '_blank']) !!}
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
                id="Curso_id_Select" data-actions-box="true" data-selected-text-format="values" name="id_curso"
                tabindex="-98">
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
                name="AnoCurricular_id[]" tabindex="-98" multiple>
            </select>
        </div>
    </div>

    <input type="hidden" value="0" id="verificarSelector">
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="">
    <input type="hidden" id="data_html" name="data_html" value="">

    <div class="card mb-2" style="float: right;">
        <button type="button" class="btn btn-success" id="gerarBtn">
            <i class="fas fa-file-pdf" id="icone_publish"></i>
            Imprimir
        </button>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            //To generate
            var id_anoLectivo = $("#lective_year");  
            var AnoCurricular_id_Select = $("#AnoCurricular_id_Select");
            var Curso_id_Select = $("#Curso_id_Select");

            $("#gerarBtn").click(function() {
                if (Curso_id_Select.val() != "" && AnoCurricular_id_Select.val() != "") {
                    $("#id_anoLectivo").val($("#lective_year").val());
                    $("#estatistica_id").submit();
                } else {
                    alert("Atenção! Os selectores curso e ano curricular, devem estar selecionado.");
                }
            });
            //Pegar nos cursos //E selecionar o ano curricular
            Curso_id_Select.change(function() {
                var id_cursos = Curso_id_Select.val();
                AnoCurricular(id_cursos)
            });

            function AnoCurricular(id_cursos) {
                $.ajax({
                    url: "/avaliations/PegarAnoCurricular/" + id_cursos,
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
                    AnoCurricular_id_Select.empty();
                    if (data > 0) {
                        for (var i = 1; i <= data; i++)
                            AnoCurricular_id_Select.append('<option value="' + i + '">' + i + '</option>');
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
        });
    </script>
@endsection