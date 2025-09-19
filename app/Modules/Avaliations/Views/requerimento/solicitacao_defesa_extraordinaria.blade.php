@extends('layouts.generic_index_new', ['breadcrumb_super' => true])
@section('title', __('Defesa extraordinária'))

@section('page-title')
    @lang('Defesa extraordinária')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('requerimento.index') }}">Requerimentos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Defesa extraordinária</li>
@endsection

@section('selects')
    <div class="mb-2 mt-3">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
            @foreach ($lectiveYears as $lectiveYear)
                @if ($lectiveYearSelected == $lectiveYear->id)
                    <option value="{{ $lectiveYear->id }}" selected>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @else
                    <option value="{{ $lectiveYear->id }}">
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
@endsection

@section('body')
    <form action="{{ route('requerimento.solicitacao_solicitacao_defesa_extraordinaria_store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                <label>Selecionar curso</label>
                                <select name="course_id" id="courses" class="selectpicker form-control form-control-sm">
                                    <option value="" selected>Selecione o Curso</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->currentTranslation->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group col">
                                <label>Estudante</label>
                                <select name="student_id" id="students" class="selectpicker form-control form-control-sm" data-live-search="true">
                                    <option value="" selected>Selecione o Estudante</option>
                                    <!--Colocado pelo JS-->
                                </select>
                            </div>
                        </div>
                        <!--Disciplinas-->
                        <div class="col-6">
                            <div class="form-group col">
                                <label>Disciplinas</label>
                                <select name="disciplina_id" id="disciplina" class="selectpicker form-control form-control-sm" data-live-search="true">
                                    <option value="" selected>Seleciona a disciplina</option>
                                    <!--Colocado pelo JS-->
                                </select>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="lectiveY" value="{{ $lectiveYear->id }}" name="lective_year">
                </div>
                <hr>
                <div class="float-right">
                    <button type="submit" class="btn btn-success mb-3">
                        <i class="fas fa-plus-circle"></i>Requerer
                    </button>
                </div>
            </div>
        </div>
    </form>
  
@endsection

@section('scripts')
    @parent
<script>
    const anoLectivo = $("#lectiveY");

    // URLs
    const estudantesUrl = "{{ url('/pt/avaliations/requerimento/getEstudante_extraordinario') }}";
    const disciplinasUrl = "{{ url('/pt/avaliations/requerimento/getDisciplinas_extraordinaria') }}";

    // Atualiza o ano letivo escondido
    $("#lective_year").change(function() {
        anoLectivo.val($(this).val());
    });

    // Quando seleciona o curso -> buscar estudantes
    $("#courses").change(function() {
        const course_id = $(this).val();
        const ano = $("#lective_year").val();

        if (!course_id) return;

        let url = `${estudantesUrl}/${course_id}/${ano}`;

        fetch(url)
            .then(response => response.json())
            .then(dados => {
                console.log("Estudantes recebidos:", dados);
                const studentSelect = $("#students");
                studentSelect.empty();
                studentSelect.append(`<option value="">Selecione o Estudante</option>`);

                dados.forEach(student => {
                    studentSelect.append(
                        `<option value="${student.user_id}"  data-tokens="${student.name} ${student.student_number} ${student.email}">
                            ${student.name ?? 'Sem nome'} #${student.student_number ?? ''}(${student.email ?? 'sem email'})
                        </option>`
                    );
                });

                studentSelect.selectpicker('refresh');
            })
            .catch(err => console.error("Erro a buscar estudantes:", err));
    });

    // Quando seleciona o estudante -> buscar disciplinas
    $("#students").change(function() {
        const student_id = $(this).val();
        const course_id = $("#courses").val()
        const ano = $("#lective_year").val();

        if (!student_id) return;

        let url = `${disciplinasUrl}/${student_id}/${ano}/${course_id}`;
        console.log("URL chamada:", url);

        fetch(url)
            .then(response => response.json())
            .then(dados => {
                console.log("Disciplinas recebidas:", dados);
                const disciplinaSelect = $("#disciplina");
                disciplinaSelect.empty();
                disciplinaSelect.append(`<option value="">Seleciona a disciplina</option>`);

                dados.forEach(disciplina => {
                    disciplinaSelect.append(
                        `<option value="${disciplina.id}" data-tokens="${disciplina.code} ${disciplina.name}">
                            #(${disciplina.code}) - ${disciplina.name ?? 'Sem nome'}
                        </option>`
                    );
                });

                disciplinaSelect.selectpicker('refresh');
            })
            .catch(err => console.error("Erro a buscar disciplinas:", err));
    });
</script>

@endsection