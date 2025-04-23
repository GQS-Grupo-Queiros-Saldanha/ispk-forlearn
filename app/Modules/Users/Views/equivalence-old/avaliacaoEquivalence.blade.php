@section('title',__('Transição de equivalência'))


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



        table,
        th,
        td,
        thead {
            border: none;
        

        }

        th {
            background-color: #999;
            color: white;
            padding: 5px;
            font-size: 18pt;
            border-bottom: 1px solid white;
            border-right: 1px solid white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #FFF
        }

        tr:nth-child(odd) {
            background: #EEE
        }

        td {
            padding: 5px;
            font-size: 18pt;
            border-bottom: 1px solid white;
            border-right: 1px solid white;

        }

        tr:hover {
            cursor: pointer;
        }
    </style>

    
@endsection
@section('content')
<div class="content-panel" style="padding: 0;">
    @include('Avaliations::avaliacao.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right" > 
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="/avaliations/panel_avaliation">Avaliacao</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Transição de equivalência</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Transição de notas - equivalência')</h1>
                    </div>
                  
                    @php
                        //   $disciplines=disciplinesSelect([$dados_geral->course_id],null);
                          $flag=false;
                    @endphp

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

        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                <form action="{{ route('equivalence_student_grade.store') }}" method="POST">
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
                                            <label>Curso(s)</label>
                                            {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso','required'=>'required','id'=>'courseID']) }}

                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Estudante</label>
                                            <select data-live-search="true" required name="Studants"
                                            class="selectpicker form-control form-control-sm" id="studentID" >
                                        
                                        </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="col">
                                            <table class="table table-hover dark">

                                                <thead>
                                                    <th>#</th>
                                                    <th>CÓDIGO</th>
                                                    <th>DISCIPLINA</th>
                                                    {{-- <th>ESTADO DO PAGAMENTO</th> --}}
                                                    <th>NOTA</th>
                                                </thead>

                                                <tbody id="students">

                                                </tbody>
                                            </table>
                                       
                                        </div>
                                    </div>
                                </div>


                                <input type="hidden" id="lectiveY"  value="" name="anoLectivo" >

                                
                            </div>
                         
                            <hr>
                            <div class="float-right"  id="group_btnSubmit" hidden>
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


            //gET STUDENT ABOUT COURSE
            $("#courseID").change(function(){
                $("#students").empty();
                $("#school_name").val('');
                var course_id = $("#courseID").val();
                var lective =$("#lective_year").val();

                $.ajax({
                    url: "/users/get_students_equivalence/"+ course_id+"/"+lective,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function (result) {
                       


                        $("#studentID").prop('disabled', true);
                        $("#studentID").empty();

                        $("#studentID").append('<option selected="" value=""></option>');
                        $.each(result, function (index, row) {
                            $("#studentID").append('<option value="' + row.id + '">' + row.name + " #"+ row.student_number + " ("+ row.email +")"+ '</option>');
                        });

                        $("#studentID").prop('disabled', false);
                        $("#studentID").selectpicker('refresh');
                    },
                    error: function (dataResult) {
                        //alert('error' + result);
                    }

                });

                
            });





            //get DISCIPLINA

            $("#studentID").change(function(){
                $("#students").empty();
                $("#school_name").val('');
                var student_id = $("#studentID").val();
                var lective =$("#lective_year").val();
                var bodyData = '';
                $.ajax({
                    url: "/users/get_students_disciplines/"+ student_id,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend:function(){
                        if($("#studentID").val()==""){
                            return false;
                        }
                    },
                    success: function (result) {
                     
                        
                        if (result.length > 0) {
                            $("#students").empty();
                            var i=1;
                            var notaP;  
                            $("#lectiveY").val($("#lective_year").val());
                            $.each(result, function (index, row) {
                                bodyData += '<tr>'
                                    bodyData += "<td class='text-center fs-2'>"+ i++ +"</td>";
                                    bodyData += "<td class='text-centerfs-2'>"+row.codigo+"</td>";
                                    bodyData += "<td class='fs-2'>"+row.disciplina+"</td>";
                                    bodyData += "<input type='hidden' value='"+row.disc_id+"' name='discipline_id[]' >";
                                    notaP= row.nota!=null? row.nota:""; 
                                  
                                     // bodyData += "<td class='text-center fs-2'>"+row.state+"</td>";  
                                        bodyData += "<td class='text-center fs-2'><input type='number' value='"+notaP+"' min='10' class='form-control'  max='20' style='width:100%;' name='nota[]' required></td>";

                                bodyData += '</tr>'
                            });
                             notaP="";
                             $("#group_btnSubmit").attr('hidden',false);
                             
                             
                        }else{

                            bodyData += '<tr>'
                            bodyData +=
                            "<td colspan='4' class='text-center fs-2'>Nenhuma disciplina foi encontrado associada a equivalência do estudante selecionado.</td>";
                            
                            bodyData += '</tr>'
                            $("#group_btnSubmit").attr('hidden',true);
                        }
                        
                        
                        $("#students").append(bodyData);

            
        
                    },
                    error: function (dataResult) {
                        //alert('error' + result);
                    }

                });


            })













            });





    </script>
@endsection
