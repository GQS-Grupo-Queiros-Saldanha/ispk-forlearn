@section('title',__('Pedido de Equivalência / Transferência'))


@extends('layouts.backoffice')
@section('styles')
@parent
<style>
    .red {
        background-color: red !important;
    }

    .dt-buttons{
        float: left;
        margin-bottom: 20px; 
    }

    .dataTables_filter label{
        float: right;  
    }

    
    .dataTables_length label{
        margin-left: 10px; 
    }
    .casa-inicio{
        
    }

    .div-anolectivo{
        width:300px; 
        padding-top:16px;
        padding-right:0px;
        margin-right: 15px;  
    }

    table,
    th,
    td {
        padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;
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
                        <div class=" float-right" > 
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="/avaliations/requerimento">Requerimentos</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Pedido de Equivalência / Transferência</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Pedido de Equivalência / Transferência')</h1>
                    </div>
                  
                    <div class="col-sm-6">
                        <div class="float-right div-anolectivo">
                            <label>Selecione o ano lectivo</label>
                            <br>
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
                                              
                    </div>

                </div>
            </div>
        </div>

        {{-- INCLUI O MENU DE BOTÕES --}}
        {{-- @include('Avaliations::avaliacao.show-panel-avaliation-button') --}}

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                <form action="{{ route('transference_studant.store') }}" method="POST">
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
                                            <label>Tipo de pedido de Equivalência / Transferência</label>
                                            <select name="tipe_transference" id="tipe_transference" required class="selectpicker form-control form-control-sm" data-live-search="true" data-actions-box="false" data-selected-text-format="values" tabindex="-98">
                                                <option value="1,10">Entrada no ({{$institution}})</option>
                                                <option value="2,11">Saída do ({{$institution}})</option>
                                                </select>
                                        </div>


                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecionar curso</label>
                                            {{ Form::bsLiveSelectEmpty('courses', [], null, ['id' => 'courses', 'class' => 'form-control','required'])}}
                                        </div>


                                    </div>
                                </div>

                                <input type="hidden" id="lectiveY"  value="" name="anoLectivo">

                                <div class="row">
                                    <div class="col-6">
                                    
                                        <div class="form-group col">
                                            <label>Estudante</label>
                                            {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control','required'])}}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col" id="group">
                                               <label> Nome da instituição de origem / destino</label>
                                               <input type="text"  value="" class="form-control" name="school_name" id="school_name" placeholder="Nome da instituição" > 
                                        </div>
                                    </div>
                                </div>
                                
                                 <div class="row">
                                    <div class="col-6">
                                    
                                        <div class="form-group col">
                                            <label>Documentação entregue:</label>
                                          <textarea name="documentation" rows="4" cols="50" placeholder="Escreva aqui..."></textarea>
                                    </div>
                                    
                                    </div>
                                    </div>
                
                                    
                            <hr>
                            <div style="margin-left:20px;">
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
                $("#students").empty();
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

            $("#courses").change(function(){
                $("#students").empty();
                // $("#school_name").val('');
                var course_id = $("#courses").val();
                var type_transfere = $("#tipe_transference").val();
                

                $.ajax({
                    url: "/users/get_students_course/"+ course_id+"/"+ type_transfere,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function (result) {
                        
                        $("#students").prop('disabled', true);
                        $("#students").empty();

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
                // alert('error' + result);
                }

            });
            
        }




    </script>
@endsection
