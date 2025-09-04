@extends('layouts.generic_index_new', ['breadcrumb_super' => true])
@section('title', __('Marcação de Revisão de Prova'))

@section('page-title')
    @lang('Marcação de Revisão de Prova')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('requerimento.index') }}">Requerimentos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Marcação de Revisão de prova</li>
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
    <form action="{{ route('requerimento.solicitacao_revisao_prova_store') }}" method="POST">
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
                                <select name="student_id" id="students" class="selectpicker form-control form-control-sm">
                                    <option value="" selected>Selecione o Estudante</option>
                                    <!--Colocado pelo JS-->
                                </select>
                            </div>
                        </div>
                        <!--Disciplinas-->
                        <div class="col-6">
                            <div class="form-group col">
                                <label>Disciplinas</label>
                                <select name="disciplina_id" id="disciplia" class="selectpicker form-control form-control-sm">
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
        // Inicialização das variáveis
        const anoLectivo = $("#lectiveY");
        const baseUrl = "{{ url('/pt/avaliations/requerimento/getEstudante') }}";
        const disciplinasUrl = "{{ url('/pt/avaliations/requerimento/getEstudante') }}";
    
        anoLectivo.val($("#lective_year").val());
        
        console.log('Ano lectivo selecionado: ' + anoLectivo.val());

        // Evento de mudança no ano lectivo
        $("#lective_year").change(function() {
            anoLectivo.val($(this).val());
            console.log('Ano lectivo alterado para: ' + anoLectivo.val());
        });

        // Evento de mudança no curso selecionado
        $("#courses").change(function() {
            const course_id = $(this).val();
            const lective_year_matriculation = $("#lective_year").val();
            console.log("Evento disparado. ID do curso:", course_id);

            console.log('Curso selecionado: ' + course_id);
            
           // Requisição AJAX para buscar estudantes finalistas
            const baseUrl = "{{ url('/pt/avaliations/requerimento/getEstudante') }}";
            let ano = document.getElementById('lective_year').value;

            let url = `${baseUrl}/${course_id}/${ano}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta da rede');
                    }
                    return response.json();
                })
                .then(dados => {
                    console.log(dados);
                    const studentSelect = $("#students");
                    studentSelect.empty(); // Limpa opções antigas

                    // Adiciona uma opção vazia no início
                    studentSelect.append(`<option value=""></option>`);

                    dados.forEach(student => {
                        const nome = student.name ?? 'Sem nome';
                        const numero = student.student_number ?? 'Sem número';
                        const email = student.email ?? 'Sem email';

                        studentSelect.append(
                            `<option value="${student.id}">${nome} #${numero} (${email})</option>`
                        );
                    });

                    // Atualiza o selectpicker do Bootstrap
                    studentSelect.selectpicker('refresh');
                })
                .catch(erro => {
                    console.error('Erro no fetch:', erro);
                });

            // Requisição AJAX para buscar disciplinas
            const disciplinasUrl = "{{ url('/pt/avaliations/requerimento/getEstudante') }}";
            let ano = document.getElementById('lective_year').value;

            let url = `${baseUrl}/${course_id}/${ano}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta da rede');
                    }
                    return response.json();
                })
                .then(dados => {
                    console.log(dados);
                    const studentSelect = $("#students");
                    studentSelect.empty(); // Limpa opções antigas

                    // Adiciona uma opção vazia no início
                    studentSelect.append(`<option value=""></option>`);

                    dados.forEach(student => {
                        const nome = student.name ?? 'Sem nome';
                        const numero = student.student_number ?? 'Sem número';
                        const email = student.email ?? 'Sem email';

                        studentSelect.append(
                            `<option value="${student.id}">${nome} #${numero} (${email})</option>`
                        );
                    });

                    // Atualiza o selectpicker do Bootstrap
                    studentSelect.selectpicker('refresh');
                })
                .catch(erro => {
                    console.error('Erro no fetch:', erro);
                });



        });
    </script>
@endsection