<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'MARCAÇÃO DE PROVA')
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
    <li class="breadcrumb-item active" aria-current="page">
        @if (isset($action) && $action == 'edit')
            Edit
        @else
            Create
        @endif
    </li>
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
    <form id="id_form_Nota" method="POST"
        @if (isset($action) && $action == 'edit') action="{{ route('calendario_prova_horario.update', $calendarioHorario->id) }}"
        @else 
            action="{{ route('calendario_prova_horario.store') }}" @endif>
        @csrf
        @if (isset($action) && $action == 'edit')
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-6 p-2">
                <label>Selecione o curso</label>
                <select data-live-search="true" required class="selectpicker form-control" required="" id="course_select"
                    data-actions-box="false" data-selected-text-format="values" name="curso" tabindex="-98">
                    <option value="">Nenhum selecionado</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @if (isset($calendarioHorario->course_id) && $calendarioHorario->course_id == $course->id) selected @endif>
                            {{ $course->currentTranslation->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Seleciona o ano curricular</label>
                @php $anos = [1 => "1º ano", 2 => "2º ano", 3 => "3º ano", 4 => "4º ano", 5 => "5º ano"]; @endphp
                <select name="year_course" id="year_course_select" class="selectpicker form-control">
                    <option value="">Nenhum selecionado</option>
                    @foreach ($anos as $key => $value)
                        @if (isset($calendarioHorario->year) && $calendarioHorario->year == $key)
                            <option value="{{ $key }}" selected>{{ $value }}</option>
                        @else
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Selecione a turma</label>
                <select data-live-search="true" required class="selectpicker form-control" required="" id="class_select"
                    data-actions-box="false" data-selected-text-format="values" name="turma_id" tabindex="-98"
                    @isset($calendarioHorario->turma_id) 
                        defaultValue="{{ $calendarioHorario->turma_id }}" 
                    @else
                        defaultValue={{ old('turma_id') }}
                    @endisset>
                    <option value=""></option>
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Selecione a disciplina</label>
                <select data-live-search="true" required class="selectpicker form-control" required=""
                    id="discipline_select" data-actions-box="false" data-selected-text-format="values" name="disciplina_id"
                    tabindex="-98"
                    @isset($calendarioHorario->disciplina_id) 
                        defaultValue="{{ $calendarioHorario->disciplina_id }}" 
                    @else
                        defaultValue={{ old('disciplina_id') }}
                    @endisset>
                </select>
            </div>
            <div class="col-6 p-2" id="caixaAvalicao">
                <label>Selecione o calendário de prova</label>
                <select data-live-search="true" required class="selectpicker form-control" required=""
                    id="calendario_prova_select" data-actions-box="false" data-selected-text-format="values"
                    name="calendario_prova_id" tabindex="-98"
                    @isset($calendarioHorario->calendario_prova_id) 
                        defaultValue="{{ $calendarioHorario->calendario_prova_id }}"
                    @else
                        defaultValue={{ old('calendario_prova_id') }} 
                    @endisset>
                    <option value="">Nenhum selecionado</option>
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Seleciona o periodo</label>
                @php $periodos = ["MANHA" => "Manhã", "TARDE" => "Tarde", "NOITE" => "Noite"] @endphp
                <select name="periodo" id="periodo_select" class="selectpicker form-control">
                    <option value="">Nenhum selecionado</option>
                    @foreach ($periodos as $key => $value)
                        <option value="{{ $key }}"
                            @if (isset($calendarioHorario->periodo) && $calendarioHorario->periodo == $key) selected 
                            @elseif(old('periodo') == $key) selected @endif>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Selecione os juris</label>
                <select data-live-search="true" required class="selectpicker form-control" required=""
                    id="discipline_select" data-actions-box="false" data-selected-text-format="values" name="juris[]"
                    tabindex="-98" multiple>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id_usuario }}" @if (isset($juris) && in_array($teacher->id_usuario, $juris)) selected @endif>
                            {{ $teacher->nome_usuario . '(' . $teacher->email_usuario . ')' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label>Digita a data marcada para prova</label>
                <input type="date" name="data_prova_marcada" id="data_prova_marcada" class="back-imput form-control"
                    value="{{ $calendarioHorario->data_prova_marcada ?? old('data_prova_marcada') }}">
            </div>
            <div class="col-6 p-2">
                <label>Digita a hora de começo</label>
                <input type="time" name="hora_comeco" id="hora_comeco" class="back-imput form-control"
                    value="{{ $calendarioHorario->hora_comeco ?? old('hora_comeco') }}">
            </div>
            <div class="col-6 p-2">
                <label>Digita a hora de termino</label>
                <input type="time" name="hora_termino" id="hora_termino" class="back-imput form-control"
                    value="{{ $calendarioHorario->hora_termino ?? old('hora_termino') }}">
            </div>
        </div>
        @if (isset($action) && $action == 'edit')
            <button type="submit" class="btn btn-warning p-2 mt-2" id="btn-agendar">
                Actualizar
            </button>
        @else
            <button type="submit" class="btn btn-primary p-2 mt-2" id="btn-agendar">
                Agendar
            </button>
        @endif
    </form>
@endsection
@section('scripts-new')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            const yearSelect = $("#year_select");
            const classSelect = $("#class_select");
            const courseSelect = $("#course_select");
            const disciplineSelect = $("#discipline_select");
            const yearCourseSelect = $('#year_course_select');
            const calendarioProvaSelect = $("#calendario_prova_select");

            const horaComeco = $("#hora_comeco");
            const horaTermino = $("#hora_termino");
            const btnAgendar = $("#btn-agendar");
            const dataProvaMarcada = $("#data_prova_marcada");
            const periodoProva = $("#periodo_select");

            const CALENDAR_OPTION = "option_prova_";

            const periodos = [{
                    name: "MANHA",
                    desc: "Manhã",
                    start: "7:00",
                    end: "12:00"
                },
                {
                    name: "TARDE",
                    desc: "Tarde",
                    start: "13:00",
                    end: "18:00"
                },
                {
                    name: "NOITE",
                    desc: "Noite",
                    start: "19:00",
                    end: "22:00"
                },
            ];

            function ajaxCalendarionProva() {
                let lective = yearSelect.val();
                if (lective != "") {
                    $.ajax({
                        url: `{{ route('ajax.calendario_prova') }}?year_id=${lective}`,
                    }).done((data) => {
                        let defaultValue = calendarioProvaSelect.attr('defaultValue');
                        let valueSelected = defaultValue !== undefined ? defaultValue + "" : -1;
                        calendarioProvaSelect.empty();
                        calendarioProvaSelect.append('<option value="">Nenhum selecionado</option>');
                        data.forEach((item) => {
                            calendarioProvaSelect.append(
                                `<option value=${item.id} id="${CALENDAR_OPTION+item.id}" start="${item.date_start}" end="${item.data_end}"
                            ${item.id == valueSelected ? 'selected': ''}>
                            ${item.code+" | "+item.display_name+ " | "+item.date_start+" à "+item.data_end}
                            </option>`
                            );
                        });
                        calendarioProvaSelect.selectpicker('refresh');
                    }).fail((jqXHR, status, error) => {

                    });
                }
            }

            function ajaxClass(yearCourse = "") {
                let course = courseSelect.val();
                if (course != "") {
                    let inner = yearCourse != "" ? "&year_course=" + yearCourse : "";
                    $.ajax({
                        url: `{{ route('ajax.class') }}?year=${yearSelect.val()}&course=${course}` + inner,
                    }).done((data) => {
                        let defaultValue = classSelect.attr('defaultValue');
                        let valueSelected = defaultValue !== undefined ? defaultValue + "" : -1;
                        classSelect.empty();
                        classSelect.append('<option value="">Seleciona a turma</option>');
                        data.forEach(item => {
                            classSelect.append(
                                `<option value=${item.class_id} ${item.class_id == valueSelected ? 'selected': ''}>
                                ${item.class+"\\"+item.sala}
                            </option>`);
                        });
                        classSelect.selectpicker('refresh');
                    }).fail((jqXHR, status, error) => {

                    });
                }
            }

            function ajaxDiscipline() {
                let course = courseSelect.val();
                let yearCourse = yearCourseSelect.val();
                if (course != "" && yearCourse != "") {
                    $.ajax({
                        url: `{{ route('ajax.discipline') }}?couser_id=${course}&yearCourse=${yearCourse}`,
                    }).done((data) => {
                        let defaultValue = disciplineSelect.attr('defaultValue');
                        let valueSelected = defaultValue !== undefined ? defaultValue + "" : -1;
                        disciplineSelect.empty();
                        disciplineSelect.append('<option value="">Seleciona a disciplina</option>');
                        data.forEach(item => {
                            disciplineSelect.append(
                                `<option value=${item.discipline_id} ${item.discipline_id == valueSelected ? 'selected': ''}>${"#"+item.code+" - "+item.dt_display_name}</option>`
                            );
                        });
                        disciplineSelect.removeAttr('disabled');
                        disciplineSelect.selectpicker('refresh');
                    }).fail((jqXHR, status, error) => {

                    });
                }
            }

            function isHoraComecoGreater() {
                let comeco = horaComeco.val();
                let termino = horaTermino.val();
                if (comeco == "" || termino == "") return false;
                return comeco >= termino;
            }

            function analisarHoras() {
                if (isHoraComecoGreater()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Conflito entre o intervalo de hora!',
                        text: 'A hora de começo não pode ser maior ou igual a hora de termino'
                    })
                    if (!btnAgendar.hasClass('d-none')) {
                        btnAgendar.addClass("d-none");
                    }
                    return false;
                } else {
                    if (horaComeco.val() == "" || horaTermino.val() == "")
                        return false;

                    if (btnAgendar.hasClass('d-none')) {
                        btnAgendar.removeClass("d-none");
                    }
                    return true;
                }
            }

            function convertDate(horaString) {
                let partes = horaString.split(':');
                let hora = parseInt(partes[0]);
                let minutos = parseInt(partes[1]);
                let data = new Date();
                let zone = {
                    timezone: 'Africa/Luanda'
                };
                data.setHours(hora);
                data.setMinutes(minutos)
                return data.toLocaleString('pt-PT', zone);
            }

            function analisarIntevaloHoras() {
                if (horaComeco.val() != "" && horaTermino.val() != "") {
                    periodos.forEach(item => {
                        if (item.name == periodoProva.val()) {
                            let proOne = convertDate(item.start) <= convertDate(horaComeco.val());
                            let proTwo = convertDate(item.end) >= convertDate(horaTermino.val());
                            if (proOne && proTwo) {
                                if (btnAgendar.hasClass('d-none')) {
                                    btnAgendar.removeClass("d-none");
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: `Conflito entre o intervalo de hora com o periódo!`,
                                    text: `A hora de começo ou de termino não esta válida no horário de periódo ${item.desc} que começa (${item.start}) à (${item.end})`
                                })
                                if (!btnAgendar.hasClass('d-none')) {
                                    btnAgendar.addClass("d-none");
                                }
                            }
                        }
                    });
                }
            }

            function changeDateInterval(calendario) {
                let optionCalendario = $(`#${CALENDAR_OPTION+calendario}`);
                dataProvaMarcada.val(optionCalendario.attr('start'));
                dataProvaMarcada.attr('min', optionCalendario.attr('start'))
                dataProvaMarcada.attr('max', optionCalendario.attr('end'))
            }

            ajaxClass();
            ajaxDiscipline();
            ajaxCalendarionProva();

            calendarioProvaSelect.on("change", (e) => {
                let value = calendarioProvaSelect.val();
                if (value != "") {
                    changeDateInterval(value);
                    dataProvaMarcada.removeAttr('disabled');
                } else {
                    dataProvaMarcada.attr('disabled', true);
                }
            });

            yearSelect.on("change", (e) => {
                ajaxClass();
                ajaxCalendarionProva();
            });

            courseSelect.on('change', (e) => {
                ajaxClass();
                ajaxDiscipline();
            });

            yearCourseSelect.on('change', (e) => {
                let valueYear = yearCourseSelect.val();
                if (valueYear != "")
                    ajaxClass(valueYear);
                else {
                    ajaxClass();
                }
                ajaxDiscipline();
            })

            //--------------------------------------------------

            periodoProva.on("change", (e) => {
                if (!(horaComeco.val() == "" || horaTermino.val() == ""))
                    analisarIntevaloHoras();
            });

            horaComeco.on("blur", (e) => {
                if (analisarHoras()) {
                    analisarIntevaloHoras();
                }
            });

            horaTermino.on("blur", (e) => {
                if (analisarHoras()) {
                    analisarIntevaloHoras();
                }
            });

            dataProvaMarcada.on("change", (e) => {
                let info = dataProvaMarcada.val();
                let data = new Date(info);
                let diaDaSemana = data.getDay();
                if (!(diaDaSemana >= 1 && diaDaSemana <= 5)) {
                    Swal.fire({
                        icon: 'error',
                        title: `Conflito no dia da semana na data marcada!`,
                        text: `A data marcada (${info}) o dia da semana não é um dia de trabalho útil (sábado ou domingo)`
                    });
                    if (!btnAgendar.hasClass('d-none')) {
                        btnAgendar.addClass("d-none");
                    }
                } else {
                    if (btnAgendar.hasClass('d-none')) {
                        btnAgendar.removeClass("d-none");
                    }
                }
            });
        })();
    </script>
@endsection
