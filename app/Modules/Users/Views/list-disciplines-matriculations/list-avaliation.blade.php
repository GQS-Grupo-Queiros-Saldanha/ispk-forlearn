@switch($type)
    @case('segunda_chamada')
        <title>Lista - Avaliações Contínuas (2ª chamada)</title>
    @break

    @case('recurso')
        <title>Lista do Exame de Recurso</title>
    @break

    @case('exame_especial')
        <title>Lista do Exame Especial</title>
    @break

    @case('melhoria_nota')
        <title>Lista do Exame de Melhoria de Nota</title>
    @break

    @CASE('exame_extraordinario')
        <title>Lista do Exame Extraordinário</title>
    @break
@endswitch


@extends('layouts.generic_index_new')
@switch($type)
    @case('segunda_chamada')
        @section('page-title', 'Lista - Avaliações Contínuas (2ª chamada)')
    @break

    @case('recurso')
        @section('page-title', 'Lista do Exame de Recurso')
    @break

    @case('exame_especial')
        @section('page-title', 'Lista do Exame Especial')
    @break

    @case('melhoria_nota')
        @section('page-title', 'Lista do Exame de Melhoria de Nota')
    @break

    @case('exame_extraordinario')
        @section('page-title', 'Lista do Exame Extraordinário')
    @break
@endswitch

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    @switch($type)
        @case('segunda_chamada')
            <li class="breadcrumb-item active" aria-current="page">Lista - Avaliações Contínuas (2ª chamada)</li>
        @break

        @case('recurso')
            <li class="breadcrumb-item active" aria-current="page">Lista do Exame de Recurso</li>
        @break

        @case('exame_especial')
            <li class="breadcrumb-item active" aria-current="page">Lista do Exame Especial</li>
        @break

        @case('melhoria_nota')
            <li class="breadcrumb-item active" aria-current="page">Lista do Exame de Melhoria de Nota</li>
        @break

        @case('exame_extraordinario')
            <li class="breadcrumb-item active" aria-current="page">Lista do Exame Extraordinário</li>
        @break
    @endswitch


@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <div class="row">
        <div class="col">

            {!! Form::open([
                'route' => ['student_evaluation_list_pdf', $type],
                'method' => 'post',
                'required' => 'required',
                'target' => '_blank',
            ]) !!}
            @csrf
            @method('post')
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group col">
                            <label>@lang('GA::courses.course')</label>
                            {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso', 'required' => 'required']) }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <label>@lang('Ano curricular')</label>
                            {{ Form::bsLiveSelectEmpty('curricular_year', [], null, ['disabled', 'placeholder' => 'Selecione o ano curricular', 'required' => 'required', 'tittle' => 'Selecione o ano curricular']) }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <label>@lang('GA::disciplines.discipline')</label>
                            {{ Form::bsLiveSelectEmpty('discipline', [], null, ['disabled', 'placeholder' => 'Selecione a disciplina', 'required' => 'required', 'tittle' => 'Selecione a disciplina']) }}
                        </div>
                    </div>
                    @if ($type != 'melhoria_nota')
                        <div class="col-6" id="disciplines-container">
                            <div class="form-group col">
                                <label>@lang('Turma')</label>
                                {{ Form::bsLiveSelectEmpty('classe', [], null, ['disabled', 'placeholder' => 'Selecione a turma', 'required' => 'required', 'tittle' => 'Selecione a turma']) }}
                            </div>
                        </div>
                    @endif
                    @if ($type == 'segunda_chamada')
                        <div class="col-6" id="av-container">
                            <div class="form-group col">
                                <label>Avaliação</label>
                                {{ Form::bsLiveSelectEmpty('avaliation', [], null, ['disabled', 'placeholder' => 'Selecione a avaliação', 'required' => 'required', 'title' => 'Selecione a avaliação']) }}
                            </div>
                        </div>
                        <div class="col-6" id="metrics-container">
                            <div class="form-group col">
                                <label>Métrica</label>
                                {{ Form::bsLiveSelectEmpty('metric', [], null, ['disabled', 'placeholder' => 'Selecione a métrica', 'required' => 'required', 'title' => 'Selecione a métrica']) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <input type="hidden" name="AnoLectivo" value="" id="Ano_lectivo_foi">
        </div>
        <div class="col-12 justify-content-md-end">
            <div class="form-group col-4  justify-content-md-end" style="float:right;">
                <button type="submit" id="btn-listar" class="btn btn-primary  float-end" target="_blank"
                    style="width:180px;">
                    <i class="fas fa-file-pdf"></i>
                    Gerar PDF
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    <script>
        (() => {
            let lective_year = $('#lective_year').val();
            let selectCourse = $('#course');
            let selectCurricularYear = $('#curricular_year');
            let selectClasse = $('#classe');
            let containerDisciplines = $('#disciplines-container');
            let selectDiscipline = $('#discipline');
            let containerGrade = $('#grade-container');
            let selectAvaluation = $('#avaliation');
            let selectMetric = $('#metric');
            $("#Ano_lectivo_foi").val(lective_year);

            selectCourse.change(function() {
                let dados = @json($courses);
                selectDiscipline.empty();
                console.clear();

                selectCurricularYear.empty();
                selectDiscipline.empty();
                selectClasse.empty();

                $.each(dados, function(indexInArray, valueOfElement) {

                    if (selectCourse.val() == valueOfElement.id) {
                        selectCurricularYear.append(
                            '<option value="" style="display:none;">Selecione o ano curricular</option>'
                        );
                        for (let index = 1; index <= valueOfElement
                            .duration_value; index++) {
                            let IdAno = index;
                            let AnoDisplay_name = index + 'º Ano';
                            selectCurricularYear.append('<option value="' + IdAno + '">' +
                                AnoDisplay_name + '</option>');
                        }
                    }

                });
                selectCurricularYear.prop('disabled', false);
                selectCurricularYear.selectpicker('refresh');
            });

            selectCurricularYear.change(function() {
                id_curso = selectCourse.val();
                anoCurricular = selectCurricularYear.val();
                PegaDisciplina(id_curso, anoCurricular);
                PegaTurma();
            });
            @if ($type != 'melhoria_nota')
                selectDiscipline.change(function() {
                    let Value = selectDiscipline.val();
                    PegaTurma();
                });
            @endif

            function PegaDisciplina(idCurso, AnoCurricular) {
                $.ajax({
                    type: "get",
                    url: '/pt/users/getDisciplina/' + idCurso + '/' + AnoCurricular,
                    data: "data",
                    success: function(response) {
                        if (response.length) {
                            selectDiscipline.empty();
                            selectDiscipline.append(
                                '<option value="" style="display:none;">Selecione a disciplina</option>'
                            );
                            $.each(response, function(indexInArray, valueOfElement) {
                                let discId = valueOfElement.id;
                                let discName = '#' + valueOfElement.code + ' - ' + valueOfElement
                                    .display_name;
                                selectDiscipline.append('<option value="' + discId + '">' +
                                    discName + '</option>');
                            });
                            selectDiscipline.prop('disabled', false);
                            selectDiscipline.selectpicker('refresh');
                        }
                    }
                });
            }

            function PegaTurma() {
                let lective_year = $('#lective_year').val();
                let curso = selectCourse.val();
                let anoCurricular = selectCurricularYear.val();
                $("#Ano_lectivo_foi").val(lective_year);
                $.ajax({
                    type: "get",
                    url: '/pt/users/turma/' + curso + '/' + lective_year + '/' + anoCurricular,
                    data: "data",
                    success: function(response) {
                        if (response.length) {
                            selectClasse.empty();
                            selectClasse.append(
                                '<option value="" style="display:none;">Selecione a turma</option>');
                            $.each(response, function(indexInArray, valueOfElement) {
                                let turmaId = valueOfElement.id;
                                console.log(valueOfElement)
                                let turmaName = '#' + valueOfElement.turma;
                                selectClasse.append('<option value="' + turmaId + '">' + turmaName +
                                    '</option>');
                            });
                            selectClasse.prop('disabled', false);
                            selectClasse.selectpicker('refresh');

                        }
                    }
                });
            }
            @if ($type == 'segunda_chamada')
                selectClasse.change(function() {
                    selectAvaluation.empty();
                    get_avaliations();
                });
            @endif

            function get_avaliations() {
                //setando as avaliacoes
                let Value = selectDiscipline.val();

                let disciplinaId = selectDiscipline.val();
                if (!disciplinaId) {
                    console.warn('Disciplina não selecionada ainda.');
                    return;
                }

                $("#Ano_lectivo_foi").val(lective_year);
                $.ajax({
                    type: "get",
                    url: '/pt/users/get_avaliacoes/' + Value + '/' + lective_year,

                    success: function(response) {



                        if (response.length) {
                            selectAvaluation.empty();

                            selectAvaluation.append(
                                '<option value="" style="display:none;">Selecione a avaliação</option>');
                            $.each(response, function(indexInArray, valueOfElement) {
                                let avaliationId = valueOfElement.id;
                                let avaliationName = valueOfElement.nome;
                                selectAvaluation.append('<option value="' + avaliationId + '">' +
                                    avaliationName + '</option>');
                            });
                            selectAvaluation.prop('disabled', false);
                            selectAvaluation.selectpicker('refresh');
                            $('avaliations-container').attr('hidden', 'false');

                        }
                    }
                });
            }


            selectAvaluation.change(function() {
                selectMetric.empty();
                get_metrics();
            });

            function get_metrics() {
                $.ajax({

                    url: '/pt/avaliations/metrica_ajax_coordenador/' + selectAvaluation.val(),
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {},
                    success: function(response) {



                        if (response.metricas.length) {
                            selectMetric.empty();

                            selectMetric.append(
                                '<option value="" style="display:none;">Selecione a avaliação</option>');
                            $.each(response.metricas, function(indexInArray, valueOfElement) {
                                let avaliationId = valueOfElement.id;
                                let avaliationName = valueOfElement.nome;
                                selectMetric.append('<option value="' + avaliationId + '">' +
                                    avaliationName + '</option>');
                            });
                            selectMetric.prop('disabled', false);
                            selectMetric.selectpicker('refresh');
                            $('avaliations-container').attr('hidden', 'false');

                        }
                    }
                });
            }



        })();
    </script>
@endsection
