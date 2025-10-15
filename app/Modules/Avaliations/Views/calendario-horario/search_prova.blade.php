<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PESQUISAR PROVAS AGENDADAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('calendario_prova_horario.index') }}">Calendário de agendamento de prova</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Pesquisar</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="year_select">Selecione o ano lectivo</label>
        <select name="lective_year" id="year_select" class="selectpicker form-control form-control-sm">
            <option selected value="">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected->id == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
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
    <form action="{{ route('calendario_prova_horario.search.post') }}" id="" method="POST" target="_blank">
        @csrf
        <div class="row">
            <div class="col-6 p-2">
                <label>Selecione o curso</label>
                <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                    data-actions-box="false" data-selected-text-format="values" name="course" tabindex="-98"
                    id="course_select" multiple>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->currentTranslation->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Seleciona o ano curricular</label>
                @php $anos = ["1" => "1º ano", "2" => "2º ano", "3" => "3º ano", "4" => "4º ano", "5" => "5º ano"] @endphp
                <select name="year_course" id="year_course_select" class="back-imput form-control">
                    @foreach ($anos as $key => $value)
                        <option value="{{ $key }}">{{ $value }} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Selecione a turma</label>
                <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                    id="class_select" data-actions-box="false" data-selected-text-format="values" name="turma"
                    tabindex="-98" multiple
                    @isset($calendarioHorario->turma_id) 
                            defaultValue="{{ $calendarioHorario->turma_id }}" 
                        @else
                            defaultValue={{ old('turma_id') }}
                        @endisset>
                    <option value=""></option>
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Seleciona o simestre</label>
                @php $simestres = [1 => "1", 2 => "2", 3=> "3", 4=> "4"] @endphp
                <select name="simestre" id="simestre_select" class="back-imput selectpicker form-control">
                    @foreach ($simestres as $key => $value)
                        <option value="{{ $key }}">{{ $value }}º simestre </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Seleciona o periodo</label>
                @php $periodos = ["MANHA" => "Manhã", "TARDE" => "Tarde", "NOITE" => "Noite"] @endphp
                <select multiple name="periodos[]" id="periodo_select" class="back-imput selectpicker form-control">
                    <option value="">Nenhum seleccionado</option>
                    @foreach ($periodos as $key => $value)
                        <option value="{{ $key }}">{{ $value }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary p-2 mt-2" id="btn-agendar">
            Pesquisa
        </button>
    </form>
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            const yearSelect = $("#year_select");
            const classSelect = $("#class_select");
            const courseSelect = $("#course_select");
            const yearCourseSelect = $('#year_course_select');

            ajaxClass();

            function ajaxClass() {
                let course = courseSelect.val();
                let yearCourse = yearCourseSelect.val();
                if (course != "") {
                    let inner = yearCourse != "" ? "&year_course=" + yearCourse : "";
                    $.ajax({
                        url: `{{ route('ajax.class') }}?year=${yearSelect.val()}&courses=${course}` + inner,
                    }).done((data) => {
                        let defaultValue = classSelect.attr('defaultValue');
                        let valueSelected = defaultValue !== undefined ? defaultValue + "" : -1;
                        classSelect.empty();
                        classSelect.append('<option value="">Seleciona a turma</option>');
                        data.forEach(item => {
                            htmlGroup =
                                `<optgroup label="${item.course.current_translation.display_name}">`;
                            item.turmas.forEach(turm => {
                                htmlGroup +=
                                    `<option value="${turm.class_id+"@"+turm.courses_id}">${turm.class+" - "+turm.sala}</option>`
                            })
                            htmlGroup += '</optgroup>';
                            classSelect.append(htmlGroup);
                        });
                        classSelect.selectpicker('refresh');
                    }).fail((jqXHR, status, error) => {

                    });
                }
            }

            courseSelect.on('change', (e) => {
                ajaxClass();
            });

            yearCourseSelect.on('change', (e) => {
                ajaxClass();
            });
        })();
    </script>
@endsection
