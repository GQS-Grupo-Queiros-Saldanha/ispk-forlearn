<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'ATRIBUIR NOTAS - REPARAÇÃO DE PERCURSO ACADÉMICO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('old_student.index') }}">Lancar notas - Reparação de percurso </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Atribuir notas</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
@endsection
@section('body')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Selecione o ano letivo</th>
                <th>Selecione o curso</th>
                <th>Selecione o estudante</th>
                <th>Selecione a disciplina</th>
                <th>Selecione a pauta</th>
                <th>Nota</th>

            </tr>
        </thead>
        <tbody>

            @csrf
            <tr>
                <td>
                    <select class="form-control" name="id_lective_year" id="id_lective_year" required>
                        @foreach ($lectiveYears as $lectiveYear)
                            <option value="{{ $lectiveYear->id }}">{{ $lectiveYear->currentTranslation->display_name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select data-live-search="true" required
                        class="selectpicker form-control form-control-sm"data-actions-box="false"
                        data-selected-text-format="values" tabindex="-98" name="course" id="id_course">
                        <option>Seleciona um curso</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->currentTranslation->display_name }}</option>
                        @endforeach
                    </select>

                </td>
                <td>
                    <select data-live-search="true" required
                        class="selectpicker form-control form-control-sm"data-actions-box="false"
                        data-selected-text-format="values" tabindex="-98" name="id_student" id="id_student">


                    </select>
                </td>
                <td>
                    <select data-live-search="true" required name="id_disciplina"
                        class="selectpicker form-control form-control-sm" data-selected-text-format="values" tabindex="-98"
                        id="id_disciplina">
                        <option>Selecione uma disciplina</option>

                    </select>
                </td>

                <td>
                    <select data-live-search="true" required name="pauta_type"
                        class="selectpicker form-control form-control-sm" data-actions-box="true"
                        data-selected-text-format="values" tabindex="-98" id="pauta_type">
                        <option>Selecione uma pauta</option>
                        <option value="MAC">MAC</option>
                        <option value="Exame Escrito">Exame Escrito</option>
                        <option value="Exame Oral">Exame Oral</option>
                        <option value="CF">CF</option>
                        <option value="Exame recurso">Exame recurso</option>
                        <option value="Exame Especial">Exame Especial</option>

                    </select>
                </td>



                <td>
                    <input type="number" name="grade" class="form-control w-auto" min="0" max="20"
                        value="" id="id_grade">
                </td>
            </tr>
        </tbody>
    </table>
    <hr>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">



                    <div class="float-right">
                        <button type="submit" class="btn btn-success btn-sm mb-3" id="btnSave">
                            Inserir notas
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <section>
        
        
        
        
        
        
        
        
        
        
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th score="col">#</th>
                    <th score="col">Nome</th>
                    <th score="col">Disciplina</th>
                    <th score="col">Pauta</th>
                    <th score="col">Nota antiga</th>
                    <th score="col">Nota</th>
                    <!--<th score="col">Ano lectivo</th>-->
                    <th score="col">Criado a</th>
                    <th score="col">Actualizado por</th>
                </tr>
            </thead>
            <tbody id="tabela">
            <tbody>
        <table>
            
            
    </section>
    
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        function loadStudents(courseId, id_lective_year) {
            $.ajax({
                url: '/avaliations/get_student_repair_ajax',
                type: 'GET',
                data: {
                    course_id: courseId,
                    id_lective_year: id_lective_year
                },
                success: function(data) {

                    var studentSelect = $('select[name="id_student"]');
                    studentSelect.empty();
                    studentSelect.append('<option value="">Selecione o estudante</option>')
                    $.each(data, function(index, student) {
                        studentSelect.append('<option value="' + student.id + '">' + student.name +
                            '#' + student.mtcode + '(' + student.email + ')</option>');
                    });

                    studentSelect.selectpicker('refresh');
                },
                error: function() {
                    alert('Ocorreu um erro ao carregar os estudantes.');
                }
            });
        }



        function loadStudentsDiscipline(id_matriculation) {

            $.ajax({
                url: '/avaliations/get_student_disciplines_repair_ajax',
                type: 'GET',
                data: {
                    id_matriculation: id_matriculation

                },
                success: function(data) {

                    var studentDisciplineSelect = $('select[name="id_disciplina');
                    studentDisciplineSelect.empty();
                    studentDisciplineSelect.append('<option value="">Selecione o estudante</option>')
                    $.each(data, function(index, discipline) {
                        studentDisciplineSelect.append('<option value="' + discipline.id + '">#' +
                            discipline.code + ' - ' + discipline.display_name + '</option>');
                    });

                    studentDisciplineSelect.selectpicker('refresh');
                },
                error: function() {
                    alert('Ocorreu um erro ao carregar os disciplinas.');
                }
            });
        }



        function storeStudentGrade(discipline_id, id_matriculation, grade, lective_year, pauta_type) {

            $.ajax({
                url: '{{ route('store_grade_student_repair') }}',
                method: 'POST',
                data: {
                    discipline_id: discipline_id,
                    id_matriculation: id_matriculation,
                    grade: grade,
                    lective_year: lective_year,
                    pauta_type: pauta_type,
                    _token: '{{ csrf_token() }}'

                },
                success: function(data) {
                    if (data == 1) {


                        Swal.fire({
                            icon: 'success',
                            title: 'Nota atribuída com sucesso!',
                            text: 'A nota foi registrada corretamente.',
                            confirmButtonText: 'Ok'
                        });

                        $("#id_grade").val("");
                    }


                },
                error: function(error) {
                    console.log(error)
                   
                }
            });
        }

        



        $("#id_course").on('change', function() {
            var courseId = $(this).val();
            var id_lective_year = $("#id_lective_year").val();

            if (courseId && id_lective_year) {
                loadStudents(courseId, id_lective_year);
                 $('#tabela').empty();
            }
        });


        $("#id_student").on('change', function() {

            var id_matriculation = $(this).val();
            if (id_matriculation) {
                loadStudentsDiscipline(id_matriculation);
                
                let lective_year = $("#id_lective_year").val();
                
                console.log(lective_year);
                
                
                //:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
          

                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                //:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
                $.ajax({
                    url: '/avaliations/get_student_data',
                     type: 'GET',
                     data: {
                         student_id: id_matriculation,
                         lective_year:  lective_year,
                     },
                         success: function(data) {
						 $('#tabela').empty();

						// Itera sobre os dados recebidos e preenche a tabela
						$.each(data, function(index, item) {
							var row = `<tr>
											<td>${index + 1}</td>
											<td>${item.nome}</td>
											<td>${item.discplina}</td>
											<td>${item.pauta}</td>
											<td>${item.notaAntiga}</td>
											<td>${item.nota}</td>
											<td>${item.created_at}</td>
											<td>${item.created_by}</td>
									   </tr>`;
							$('#tabela').append(row);
						});
					},
                     error: function() {
                     alert('Ocorreu um erro ao carregar os dados do estudante.');
                     }
                 });
                
            }

        });


        $("#btnSave").on('click', function(e) {
            e.preventDefault();

            const studentIDMatriculation = $("#id_student").val();
            const studentDiscipline = $("#id_disciplina").val();
            const id_lective_year = $("#id_lective_year").val();
            const grade = $("#id_grade").val();
            const pauta_type = $("#pauta_type").val();

            if (studentDiscipline && studentIDMatriculation && grade && id_lective_year && pauta_type) {

                storeStudentGrade(studentDiscipline, studentIDMatriculation, grade, id_lective_year, pauta_type);
            } else {
                alert("Por favor, preencha todos os campos obrigatórios");
                return false;
            }
        });


        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
