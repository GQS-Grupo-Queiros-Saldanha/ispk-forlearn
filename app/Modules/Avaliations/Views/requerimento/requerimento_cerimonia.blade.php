<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>



@section('title', __('Cerimonia de Outorga'))
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
                                <li class="breadcrumb-item active" aria-current="page">Cerimonia de Outorga</li>
                            </ol>
                        </div>
                    </div>
                </div>


                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Cerimonia de Outorga</h1>
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
                        {!! Form::open(['route' => ['requerimento.store_doc_cerimonia'], 'target' => '_blank']) !!}
                        <div class="row">

                            <div class="col-6 emo">
                                <div class="form-group col">
                                    <label for="student">Estudantes</label>
                                    <select class="selectpicker form-control " name="student" id="student"
                                        data-actions-box="true" data-live-search="true">

                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="emolumentos">Emolumentos</label>
                                    <select class="selectpicker form-control " name="emolumentos[]" id="emolumentos"
                                        data-actions-box="true" data-live-search="true" multiple>
                                        @foreach ($emolumentos as $item)

                                            @switch(strtolower($item->nome))
                                                @case("certificado")
                                                <option value="{{ $item->id_article }}-{{$item->money}}-4"
                                                    selected>{{ $item->nome }}</option>  
                                                    @break
                                                @case("diploma")
                                                <option value="{{ $item->id_article }}-{{ $item->money }}-5"
                                                    selected>{{ $item->nome }}</option>
                                                    @break
                                                    @default
                                                    <option value="{{ $item->id_article }}-{{ $item->money }}-0"
                                                        selected>{{ $item->nome }}</option>                                                    
                                            @endswitch
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 data-final">
                                <div class="form-group col data-final">
                                    <label for="dataconclusao">Data de conclusao:</label>
                                    <input type="date" max="{{date("Y-m-d")}}" class="form-control" id="dataconclusao" name="dataconclusao" required>
                                </div> 
                            </div>
                            <div class="col-6 folha">
                                <div class="form-group col data-final">
                                    <label for="folha">Nº de folha  (Último: {{$diploma}})</label> 
                                    <input type="type" min="1766" class="form-control" id="folha" name="folha" required>
                                </div> 
                            </div>
                            <div class="col-6 data-final">
                                <div class="form-group col data-final">
                                    <label for="data_outorga">Data de Outorga:</label>
                                    <input type="date" class="form-control" id="data_outorga" name="data_outorga" required>
                                </div> 
                            </div>

                        </div>

                        <hr>
                        <div class="float-right">
                            <button type="submit" class="btn btn-success mb-3" id="requerer">
                                <i class="fas fa-plus-circle"></i>
                                Requerer documento
                            </button>

                        </div>

                        <div class="row">
                            <div class="col-4 mr-3">
                                <div class="form-group">
                                    <div class="alert alert-warning alert-dismissible fade show alert_request"
                                        role="alert">
                                        <strong><i class="fas fa-exclamation-triangle"
                                                style="margin-right: 10px;"></i></strong><strong class="title_requerimento"
                                            style="font-size: 20px;"></strong> <br>
                                        <p id="sms" style="font-size: 20px;"></p>
                                    </div>
                                </div>
                            </div>
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
        var sms = $("#sms");
        var btn_requerer = $("#requerer");
        var alert = $(".alert_request");
        var title = $(".title_requerimento");
        var folha = $("#folha");

        

        folha.change(function () {
            $.ajax({
                url: "/avaliations/requerimento_folha/"+folha.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {
                
                if (data["estado"]==1) {
                    folha.addClass("is-invalid");
                }else{
                    folha.removeClass("is-invalid");
                } 
            });    
        });

        btn_requerer.hide();
        alert.hide();
        getUser();

        $("#emolumentos,#student,#lective_year,#dataconclusao").on("change", function() {
            validar();
        });



        $("#lective_year").on('change', function() {
            getUser();
            validar();
            getUserArticle();
        });

        function getUserArticle() {

            $.ajax({
                url: "/avaliations/requerimento_articles_cerimonia/"+$("#lective_year").val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                emolumentos.empty();

                data.forEach(function(article) {

                var doc = article.nome.toLowerCase();
                switch (doc) {
                        case "certificado":
                            emolumentos.append('<option value="'+article.id_article+'-'+article.money+'-4" selected>'+article.nome+'</option>');                    
                            break;
                    
                        case "diploma":
                        emolumentos.append('<option value="'+article.id_article+'-'+article.money+'-5" selected>'+article.nome+'</option>');                    
                        break;
                        
                        default:
                        emolumentos.append('<option value="'+article.id_article+'-'+article.money+'-0" selected>'+article.nome+'</option>');                    
                        break;
                }
                });

                emolumentos.selectpicker('refresh');

            });
        }



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
                url: "/avaliations/matriculation_requerimento/" + ano_lectivo.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                student.empty();
                data["matriculation"].forEach(function(user) {
                    student.append('<option value="' + user.codigo + '">' + user.name + ' #' + user.matricula + ' ( ' + user.email +
                        ' )</option>');
                });
                student.selectpicker('refresh');
            });
        }

        // Requerer documento

        btn_requerer.click(function() {
            store_doc();
        });


        function validar() {



            if (emolumentos.val() == null || student.val() == null) {
                btn_requerer.hide();
            } else {
                btn_requerer.show();
            }
        }
    </script>
@endsection
