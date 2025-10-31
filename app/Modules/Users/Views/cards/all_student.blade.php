<title>Gestão de cartões</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Gestão de cartões')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Gestão de cartões</li>
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
                'route' => ['cards.report'],
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
                    <div class="col-6" id="disciplines-container">
                        <div class="form-group col">
                            <label>@lang('Turma')</label>
                            {{ Form::bsLiveSelectEmpty('classe', [], null, ['disabled', 'placeholder' => 'Selecione a turma', 'required' => 'required', 'tittle' => 'Selecione a turma']) }}
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="AnoLectivo" value="" id="Ano_lectivo_foi">
        </div>
        <div class="col-12 ">

            <div class="form-group col-4  d-flex justify-content-end" style="float:right;">
                <button type="submit" id="btn-listar" class="btn btn-primary  float-end" target="_blank" 
                    style="width:180px;">
                    <i class="fas fa-file-pdf"></i>
                    Gerar PDF
                </button>
          
                <a  href="/pt/users/cards/report_all/{{$lectiveYearSelected }}/1" class="btn btn-dark text-white  float-end report-all" target="_blank"
                    style="width:180px;" id="report_all">
                    <i class="fas fa-file-pdf"></i>
                    Estatística
                </a> 
            </div>
        </div>
        {!! Form::close() !!}
        <div class="col-12 justify-content-md-end">

            
        </div>
    </div>
    <table id="cards-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Matrícula</th>
                <th>Estudante</th>
                <th>Email</th>
                <th>Curso</th>
                <th class="text-center">Fotografia</th>
                <th>Impressão</th>
                <th>Entrega</th>
                <th>Actividades</th>
            </tr>
        </thead>
    </table>
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

            $("#Ano_lectivo_foi").val(lective_year);

            selectCourse.change(function() {
                let dados = @json($courses);
                selectDiscipline.empty();
                 

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
                PegaTurma();
            });

            selectDiscipline.change(function() {
                let Value = selectDiscipline.val();
                PegaTurma();
            });

            selectClasse.change(function() {
                PegaEstudantes();
            });

            
           $("#lective_year").change(function() {
                let anoLectivo = $("#lective_year").val() || "{{ $lectiveYearSelected }}";
                $("#report_all").attr("href", "/pt/users/cards/report_all/" + anoLectivo + "/1");
            });




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
                         
            function PegaEstudantes(){
                let lective_year = $('#lective_year').val();
                let curso = selectCourse.val();
                let anoCurricular = selectCurricularYear.val();

                var AnoDataTable = $('#cards-table').DataTable({
                    ajax: {
                        "url": '/pt/users/cards/all_student_ajax/'+selectClasse.val()+'/' + curso + '/' + lective_year + '/' + anoCurricular,
                        "type": "GET"
                    },
                    destroy: true,
                    buttons: [
                        'colvis',
                        'excel'
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'matricula',
                            name: 'matricula',
                            searchable: true
                        },
                        {
                            data: 'student',
                            name: 'student',
                            searchable: true
                        },
                        {
                            data: 'email',
                            name: 'email',
                            searchable: true
                        },
                        {
                            data: 'course_name',
                            name: 'course_name',
                            searchable: true
                        },
                        {
                            data: 'photo',
                            name: 'photo',
                            searchable: true
                        },
                        {
                            data: 'impressao',
                            name: 'impressao',
                            searchable: true
                        },
                        { 
                            data: 'entrega',
                            name: 'entrega',
                            searchable: true
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            searchable: true
                        }

                    ],

                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });
                    
            }

        })();
    </script>
@endsection
