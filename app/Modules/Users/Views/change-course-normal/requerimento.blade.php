<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

<title>Mudança de Curso | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Pedido de Mudança de Curso')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Mudança de Curso</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
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
        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                <form action="{{ route('requerir_mudanca_course_student.store') }}" method="POST">
                    @method('POST')
                    @csrf


                <div class="row">
                    <div class="col">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    ×
                                </button>
                                <h5>@choice('common.error', $errors->count())</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecionar curso</label>
                                                {{ Form::bsLiveSelectEmpty('courses', [], null, ['id' => 'courses', 'class' => 'form-control','required'])}}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Estudante</label>
                                            {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control','required'])}}
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="lectiveY"  value="" name="anoLectivo">

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecione o curso pretendido</label>
                                            {{ Form::bsLiveSelectEmpty('courses_new', [], null, ['id' => 'courses_new', 'class' => 'form-control','required'])}}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Motivo da mudança de curso</label>
                                            {{ Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control', 'required', 'rows'=>'5']) }}

                                            
                                        </div>
                                    </div>

                                
                                </div>

                            </div>
                            <hr>
                            <div class="float-right">
                                <button type="submit" class="btn btn-success mb-3">
                                    <i class="fas fa-plus-circle"></i>
                                     Guardar
                                </button>
                                
                            </div>
                </form>


            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function(){


            //Input lective year 
            $("#lectiveY").val($("#lective_year").val());

            var input=$("#group").html();
            $("#tipe_transference").change(function(){
             
                var valor=$("#tipe_transference").val();
                if(valor==2){
                   var escola="{{$institution}}";
                   $("#school_name").val(escola);
                    $("#school_name").prop("readonly",true);
                    
                }else{

                    $("#school_name").prop("readonly",false);
                    $("#school_name").val('');
                }
            });



            $("#lective_year").change(function(){
                $("#lectiveY").val($("#lective_year").val());
            });


            listCourses();



            $("#students").change(function(){
                listCoursesInvite();
            });



            $("#courses").change(function(){
                $("#students").empty();
                $("#school_name").val('');
                var course_id = $("#courses").val();

                $.ajax({
                    url: "/users/get_students_course_normal/"+ course_id,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function (result) {
                        
                        $("#students").prop('disabled', true);
                        $("#students").empty();
                        $("#courses_new").empty();

                        $("#students").append('<option selected="" value=""></option>');
                        $.each(result, function (index, row) {
                           $("#students").append('<option value="' + row.id + '">' + row.name + " #"+ row.student_number + " ("+ row.email +")"+ '</option>');
                        });

                        $("#students").prop('disabled', false);
                        $("#students").selectpicker('refresh');
                    },
                    error: function (dataResult) {
                        //alert('error' + result);
                    }

                });
            })

        });



        function listCourses(){
      
           var selectCourse = $("#courses");
            $.ajax({
                url: "/avaliations/list_courses/",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',

                success: function (result) {
                    // Salvar o objeto result no localStorage
                    if (result && result.length > 0) {
                        localStorage.setItem('cursoData', JSON.stringify(result));
                     }
                    selectCourse.prop('disabled', true);
                    selectCourse.empty();

                    selectCourse.append('<option selected="" value=""></option>');
                    $.each(result, function (index, row) {
                        selectCourse.append('<option value="' + row.id + '">' + row.current_translation.display_name + '</option>');
                      
                    });

                    selectCourse.prop('disabled', false);
                    selectCourse.selectpicker('refresh');
                },
                error: function (result) {
                }
            });
     
        }
// Pegar os cursos e preencher o select, excluindo o curso selecionado
function listCoursesInvite() {
    var id_CourseSelected = $("#courses").val();
    var selectCourse = $("#courses_new");
    var cursoData = localStorage.getItem('cursoData');

    if (cursoData) {
        // Analise os dados do localStorage de volta em um objeto JavaScript
        var data = JSON.parse(cursoData);
        // Limpe o select antes de preenchê-lo novamente
        selectCourse.empty();
        selectCourse.append('<option selected="" value=""></option>');

        // Preencha o select com os dados recuperados, excluindo o curso selecionado
        $.each(data, function (index, row) {
            if (row.id != id_CourseSelected) {
                selectCourse.append('<option value="' + row.id + '">' + row.current_translation.display_name + '</option>');
            }
        });

        // Atualize o selectpicker, se necessário
        selectCourse.selectpicker('refresh');
    } else {
        // Trate o caso em que não há dados no localStorage
        return false;
    }
}




    </script>
@endsection
