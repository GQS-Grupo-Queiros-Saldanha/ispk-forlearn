<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>



@section('title', __('Mudança de turma'))
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
                                <li class="breadcrumb-item active" aria-current="page">Mudança de Turma</li>
                            </ol>
                        </div> 
                    </div>
                </div>


                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Mudança de Turma</h1>
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
                        {!! Form::open(['route' => ['mudanca_turma_store'], 'target' => '_blank']) !!}
                        <div class="row">

                            <div class="col-6 emo">
                                <div class="form-group col">
                                    <label for="student">Estudantes</label>
                                    <select class="selectpicker form-control " name="student" id="student" required
                                        data-actions-box="true" data-live-search="true">
                                    </select>
                                </div>
                            </div>
                            
                          
                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="year">Ano curricular</label>
                                    <select class="selectpicker form-control " name="year" id="year" required
                                        data-actions-box="true" data-live-search="true">
                                            
                                    </select> 
                                </div>
                            </div>
                            <div class="col-6" hidden>
                                <div class="form-group col">
                                    <label for="old_class">Turma actual</label>
                                    <input class="selectpicker form-control" name="old_class" id="old_class"> 
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col"> 
                                    <label for="active_class">Turma actual</label>
                                    <input class="selectpicker form-control " name="" id="active_class"
                                    disabled
                                    >
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col"> 
                                    <label for="turno">Período</label>
                                    <select class="selectpicker form-control " name="turno" id="turno" required
                                        data-actions-box="true" data-live-search="true">
                                            <option value=""></option>
                                            @foreach ($turnos as $turno)
                                                <option value="{{ $turno->id }}">
                                                    {{ $turno->display_name }}
                                                </option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col"> 
                                    <label for="new_class">Nova Turma</label>
                                    <select class="selectpicker form-control " name="new_class" id="new_class"
                                        data-actions-box="true" data-live-search="true" required>
                                            <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group col"> 
                                    <label for="description">Observações</label>
                                    <textarea class="selectpicker form-control " name="description" id="description" required></textarea>
                                </div>
                            </div>
                            
                            <div hidden>
                                <input type="number" id="lective_years" name="lective_years">
                            </div>
                        </div>

                        <hr>
                        <div class="float-right">
                            <button type="submit" class="btn btn-success mb-3" id="requerer">
                                <i class="fas fa-plus-circle"></i>
                                Requerer documento
                            </button>

                        </div>

                       
                        {!! Form::close()!!}
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        var tipo_requerimento = $("#req_type");
        var emolumentos = $("#emolumentos");
        var student = $("#student");
        var ano_lectivo = $("#lective_year");
        var btn_requerer = $("#requerer");
        var alert = $(".alert_request");
        var title = $(".title_requerimento");
        var registo = $("#registo");
        var year = $("#year");
        var new_class = $("#new_class");
        var turno = $("#turno");
        var temp_old_class = null;


        $("#lective_years").val($("#lective_year").val());

     

        btn_requerer.hide();
        alert.hide(); 
        getUser();

        $("#emolumentos,#student,#lective_year,#dataconclusao").on("change", function() {
            validar();
        });



        $("#lective_year").on('change', function() {
            getUser();
            validar();
            $("#lective_years").val($("#lective_year").val());
        });

        student.on('change', function() {
            studant_get_year();
        });
        
        year.on('change', function() {
            $("#old_class").val(year.val().split(',')[1]);
            $("#active_class").val(year.val().split(',')[2]);
        });

        turno.on('change', function() {
            get_classes(year.val().split(',')[0],$("#lective_year").val(),student.val(),turno.val());
        });

    



        // Verificar o estado de mensalidade do estudante

        student.change(function() {
            // mensalidades();
        });

        function mensalidades() {

            $.ajax({
                url: "/avaliations/requerimento_ajax/" + student.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',


            }).done(function(data) {




                if (data["anos"].length == 0) {
                    student_year.empty();
                    student_year.selectpicker('refresh');
                } else {
                    student_year.empty();
                    data["anos"].forEach(function(ano_matriculado) {
                        student_year.append('<option value="' + ano_matriculado.ano + '">' +
                            ano_matriculado.ano +
                            '</option>');
                    });

                    student_year.selectpicker('refresh');
                }



            });

        }

        function getUser() {

            $.ajax({
                url: "/avaliations/requerimento/get_students_matriculation/"+ano_lectivo.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                student.empty();
                student.append('<option value=""></option>');
                data["matriculation"].forEach(function(user) {
                    student.append('<option value="' + user.matriculation_id + '">' + user.full_name + ' #' + user.matriculation+ ' ( ' + user.email +
                        ' )</option>');
                });
                student.selectpicker('refresh');
            });
        }

        function studant_get_year() {

            $.ajax({
                url: "/avaliations/requerimento/studant_get_year/"+student.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                year.empty(); 
                year.append('<option value=""></option>');
      
                data["years"].forEach(function(years) {
                    year.append('<option value="'+years.year+','+years.id+','+years.classes+'">'+years.year+'</option>');
                });
                year.selectpicker('refresh');
                year.attr('disabled',false);
            });
        }
        function get_classes(year,lective_year,student,turno) { 

            $.ajax({
                url: "/avaliations/requerimento/get_classes/"+year+"/"+lective_year+"/"+student+"/"+turno,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {
                new_class.empty(); 
                new_class.append('<option value=""></option>');
      
                data["classes"].forEach(function(classe) {
                    new_class.append('<option value="'+classe.id+'">'+classe.classes+'</option>');
                });
                new_class.selectpicker('refresh');
                new_class.attr('disabled',false);
            });
        }

        // Requerer documento

        btn_requerer.click(function() {
            store_doc();
        });


        function validar() {



            if (student.val() == null) {
                btn_requerer.hide();
            } else {
                btn_requerer.show();
            }
        }
    </script>
@endsection
