@extends('layouts.generic_index_new')
@section('page-title', 'Análise Estatístico dos Matriculados')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Estatística dos Matriculados</li>
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
                'route' => ['estatistica.matriculation.generate'],
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
                    <div class="col-6" id="disciplines-container">
                        <div class="form-group col">
                            <label>@lang('Ano curricular')</label>
                            <select name="curricular_year[]" data-live-search="true" data-actions-box="true"
                                id="curricular_year" class="selectpicker form-control form-control-sm" disabled required
                                multiple></select>
                        </div>
                    </div>
                    <div class="col-6" id="disciplines-container">
                        <div class="form-group col">
                            <label>@lang('Turma')</label>
                            <select name="classe[]" id="classe" data-live-search="true" data-actions-box="true"
                                class="selectpicker form-control form-control-sm" disabled required multiple></select>
                        </div>
                    </div>
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
    @endsection
    @section('models')
        @include('layouts.backoffice.modal_confirm')
    @endsection
    @section('scripts-new')
        @parent
        <script>
            let lective_year = $('#lective_year').val();
            let selectCourse = $('#course');
            let selectCurricularYear = $('#curricular_year');
            let selectClasse = $('#classe');
            let containerDisciplines = $('#disciplines-container');
            let selectDiscipline = $('#discipline');
            let containerGrade = $('#grade-container');

            $("#Ano_lectivo_foi").val(lective_year);
            $(document).ready(function() {

                //Change do curso para trazer as disciplinas
                selectCourse.change(function() {
                    var dados = @json($courses);
                    selectDiscipline.empty();
                    console.clear();

                    //Limpar as inputs
                    selectCurricularYear.empty();
                    selectDiscipline.empty();
                    selectClasse.empty();
                    //

                    $.each(dados, function(indexInArray, valueOfElement) {

                        if (selectCourse.val() == valueOfElement.id) {
                            // selectCurricularYear.append('<option value="" style="display:none;">Selecione o ano curricular</option>'); 
                            for (let index = 1; index <= valueOfElement.duration_value; index++) {
                                var IdAno = index;
                                var AnoDisplay_name = index + 'º Ano';
                                selectCurricularYear.append('<option value="' + IdAno + '">' +
                                    AnoDisplay_name + '</option>');

                            }
                        }

                    });
                    selectCurricularYear.prop('disabled', false);
                    selectCurricularYear.selectpicker('refresh');

                });

                //Pegar as disciplinas 
                selectCurricularYear.change(function() {
                    id_curso = selectCourse.val();
                    anoCurricular = selectCurricularYear.val();
                    // PegaDisciplina(id_curso,anoCurricular);    
                    PegaTurma();
                });

                selectDiscipline.change(function() {
                    var Value = selectDiscipline.val();

                    PegaTurma();

                });

            });

            function PegaDisciplina(idCurso, AnoCurricular) {
                $.ajax({
                    type: "get",
                    url: '/pt/users/getDisciplina/' + idCurso + '/' + AnoCurricular,
                    data: "data",
                    success: function(response) {

                        console.log(response)

                        if (response.length) {
                            selectDiscipline.empty();
                            // selectDiscipline.append('<option value="" style="display:none;">Selecione a disciplina</option>');
                            $.each(response, function(indexInArray, valueOfElement) {
                                var discId = valueOfElement.id;
                                var discName = '#' + valueOfElement.code + ' - ' + valueOfElement
                                    .display_name;
                                selectDiscipline.append('<option value="' + discId + '">' + discName +
                                    '</option>');
                            });
                            selectDiscipline.prop('disabled', false);
                            selectDiscipline.selectpicker('refresh');
                        }

                    }
                });
            }

            //Pega as turmas da disciplinas
            function PegaTurma() {
                let lective_year = $('#lective_year').val();
                var curso = selectCourse.val();
                var anoCurricular = selectCurricularYear.val();

                $("#Ano_lectivo_foi").val(lective_year);
                $.ajax({
                    type: "get",
                    url: '/pt/users/turma_estatistica/' + curso + '/' + lective_year + '/' + anoCurricular,
                    data: "data",
                    success: function(response) {
                        // alert(response) 
                        console.log(response)

                        if (response.length) {

                            selectClasse.empty();
                            selectClasse.append(
                            '<option value="" style="display:none;">Selecione a turma</option>');
                            $.each(response, function(indexInArray, valueOfElement) {
                                var turmaId = valueOfElement.id;
                                console.log(valueOfElement)
                                var turmaName = '#' + valueOfElement.turma;
                                selectClasse.append('<option value="' + turmaId + '">' + turmaName +
                                    '</option>');
                            });
                            selectClasse.prop('disabled', false);
                            selectClasse.selectpicker('refresh');
                        }
                    }
                });

            }
        </script>
    @endsection
