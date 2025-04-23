<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
@switch($type)
    @case('estagio')
        @section('title', __('Solicitação de Estágio'))
        @break
    @case('carta')
        @section('title', __('Carta de Recomendação'))
        @break
@endswitch


@extends('layouts.backoffice')
@section('styles')
@parent
    <style>
        .red {
            background-color: red !important;
        }

        .dt-buttons {
            float: left;
            margin-bottom: 20px;
        }

        .dataTables_filter label {
            float: right;
        }


        .dataTables_length label {
            margin-left: 10px;
        }

        .casa-inicio {}

        .div-anolectivo {
            width: 300px;

            padding-right: 0px;
            margin-right: 15px;
        }
    </style>

@endsection

@section('content')  
<div class="content-panel" style="padding: 0;">
    @include('Avaliations::requerimento.navbar.navbar')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class=" float-right">
                        <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                            <li class="breadcrumb-item"><a href="/avaliations/requerimento">Requerimentos</a></li>
                            @switch($type)
    @case('estagio')
    <li class="breadcrumb-item active" aria-current="page">Solicitação de Estágio</li>
        @break
    @case('carta')
    <li class="breadcrumb-item active" aria-current="page">Carta de Recomendação</li>
       
        @break
@endswitch
                            
                        </ol>
                    </div>
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-6">
                    
                    @switch($type)
    @case('estagio')
    <h1>Solicitação de Estágio</h1>
        @break
    @case('carta')
    <h1>Carta de Recomendação</h1>
       
        @break
@endswitch
                </div>
                
                <div class="col-sm-6">
                    <div class="float-right div-anolectivo">
                        <label>Selecione o ano lectivo</label>
                        <br>
                        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm"
                            style="width: 100%; !important">
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
                </div>
            </div>

        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    {!! Form::open(['route' => 'solicitacao_estagio_store', 'target' => '_blank']) !!}
                    <div class="row">

                        <!-- Campo Curso -->
                        <div class="form-group col-md-6">
                            <label for="course_id">Curso</label>
                            <select id="course_id" name="course_id" class="form-control" required
                                data-live-search="true">
                                <option value="">Selecione um curso</option>
                                @foreach($allCourses as $course)
                                    <option value="{{ $course->courses_id }}">{{ $course->course_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campo Estudantes -->
                        <div class="form-group col-md-6">
                            <label for="student_id">Estudantes</label>
                            <select id="student_id" name="students" class="form-control selectpicker" required
                                data-actions-box="true" data-live-search="true">
                                <option value="">Selecione um estudante</option>
                            </select>
                        </div>

                        <!-- Campo Nome da Instituição -->
                        <div class="form-group col-md-6">
                            <label for="nomedainstituicao_SdE">Nome da Instituição/Empresa</label>
                            <textarea class="form-control" name="nomedainstituicao_SdE" id="nomedainstituicao_SdE"
                                placeholder="Insira o nome da instituição/empresa" required></textarea>
                        </div>
                        <input type="hidden" name=type value={{$type}} id = "type">
                        <input type="hidden" id="lective_years" name="anoLectivo" value="{{ $lectiveYearSelected }}">
                    </div>

                    <hr>
                    <div class="float-right">
                        <button type="submit" class="btn btn-success mb-3" id="requerer">
                            <i class="fas fa-plus-circle"></i>
                            Requerer documento
                        </button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        $(document).ready(function () {
            let selectCourse = $('#course_id');
            let selectStudent = $('#student_id');

            // Inicializa o selectpicker para estudantes
            selectStudent.selectpicker();

            // Evento para quando o curso for mudado
            selectCourse.on('change', function () {
                selectStudent.empty();
                selectStudent.append('<option value="" selected>Selecione o estudante</option>');

                let courseId = this.value;
               
                if (courseId) {
                 
                    $.ajax({
                        url: 'get-students-by-course/'+ courseId,
                        type: 'GET',
                        success: function (response) {
                            if (response.length > 0) {
                                response.forEach(function (student) {
                                    selectStudent.append('<option value="' + student.id + '">' + student.name + '</option>');
                                });
                            } else {
                                selectStudent.append('<option value="" disabled>Nenhum estudante encontrado</option>');
                            }
                            selectStudent.prop('disabled', false);
                            selectStudent.selectpicker('refresh');
                        },
                        error: function (xhr, status, error) {
                            console.error('Erro ao buscar estudantes:', error);
                        }
                    });
                } else {
                    selectStudent.prop('disabled', true);
                }
            });
        });
    </script>









    @endsection