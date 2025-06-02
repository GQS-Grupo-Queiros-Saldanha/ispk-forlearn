<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>



@section('title', __('Documentos'))
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
                                <li class="breadcrumb-item active" aria-current="page">Documentos</li>
                            </ol>
                        </div>
                    </div>
                </div>


                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('DOCUMENTOS')</h1>
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

                        <div class="row">

                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="req_type">Tipo de documento</label>
                                    <select class="selectpicker form-control " name="req_type" id="req_type"
                                        data-actions-box="true" data-live-search="true">
                                        <option value=""></option>
                                        <!--<option value="4">Anulação de Matrícula</option>-->
                                        <option value="1">Declaração</option>
                                        <option value="2">Certificado</option>
                                        <option value="3">Diploma</option>
                                        <option value="5">Percurso Academico</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-6 emo">
                                <div class="form-group col">
                                    <label for="emolumentos_doc">Emolumentos</label>
                                    <select class="selectpicker form-control " name="emolumentos_doc" id="emolumentos_doc"
                                        data-actions-box="true" data-live-search="true">

                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-6 emo">
                                <div class="form-group col">
                                    <label for="student">Estudantes</label>
                                    <select class="selectpicker form-control " name="student" id="student"
                                        data-actions-box="true" data-live-search="true">

                                    </select>
                                </div>
                            </div>
                            <div class="col-6 emo">
                                <div class="form-group col anos_estudantes">
                                    <label for="year">Ano</label>
                                    <select class="selectpicker form-control " name="year" id="year"
                                        data-actions-box="true" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group col efeito">
                                    <label for="efeito">Para efeito de:</label>
                                    <input type="text" class="form-control" id="efeito" name="efeito">
                                </div>
                            </div>
                            <div class="col-6 data-final">
                                <div class="form-group col data-final">
                                    <label for="dataconclusao">Data de conclusão:</label>
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
                            <div class="col-6 registro">
                                <div class="form-group col data-final">
                                     <label for="registro">Nº de registro</label> 
                                    <input type="type" class="form-control" id="registro" name="registro" required>
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
          window.addEventListener('DOMContentLoaded', function () {
            document.getElementById('req_type').selectedIndex = 0;
        });
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        var tipo_requerimento = $("#req_type");
        var emolumentos_doc = $("#emolumentos_doc");
        var student = $("#student");
        var emo = $(".emo");
        var student_list = $(".student_list");
        var ano_lectivo = $("#lective_year");
        var student_year = $("#year");
        var efeito = $("#efeito");
        var dataconclusao = $("#dataconclusao"); 
        var folhaN = $("#folha");
        var data_outorga = $("#data_outorga");
        var sms = $("#sms");
        var btn_requerer = $("#requerer");
        var alert = $(".alert_request");
        var title = $(".title_requerimento");
        var n_registro = $("#registro");
        emo.hide();
        student_list.hide();
        btn_requerer.hide();
        alert.hide();
        $("#anos_estudantes,.data-final").hide();


        $("#emolumentos_doc,#student,#lective_year").on("change", function() {
            validar();
        });

        $("#req_type").on('change', function() {

            getUserArticle();
            //getUser();
            //validar();

        });

        $("#lective_year").on('change', function() {


            //getUser();
            //validar();
            getUserArticle();

        });

        $("#emolumentos_doc").change(function() {

            getUser();
            
            var tipo = $('#emolumentos_doc').val();
            tipo = tipo.split(",");

            console.log('tipo: ',tipo);
            if (tipo[2] == "2" || tipo[2] == "3") {
                $(".anos_estudantes").show();
            } else {
                $(".anos_estudantes").hide();
            }


            if (tipo[2] == "6" || tipo[2] == "1" || tipo[2] == "2" || tipo[2] == "8" || tipo[2] == "9") {
                $(".efeito").show();
            } else {
                $(".efeito").hide();
            }


        });
        
        $("#req_type").change(function() {


            var tipo = $('#req_type').val();
            tipo = tipo.split(",");


            if (tipo == "1") {
                $(".anos_estudantes,.efeito").show();
            } else {
               
                $(".anos_estudantes,.efeito").hide();
            }

            if (tipo == "3") {
                $(".data-final").show();
            }
            else {
                
                $(".data-final").hide();
            }

        });


        // Verificar o estado de mensalidade do estudante

        student.change(function() {
            mensalidades();
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

        function getUserArticle() {

            $.ajax({
                url: "/avaliations/requerimento_articles/" + [ $("#lective_year").val(), tipo_requerimento.val()],
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                emolumentos_doc.empty();


                if (data["type"] == 1) {

                    // Declarações
                    data["articles"].forEach(function(article) {

                        var doc = article.nome.toLowerCase();
                        //filtragem do id fornecido com o id da tabela article_documents!
                        const code = @json($code);
                        let filtro = article.id_article;
                        let filtrados = code.filter(item => item.article_id === filtro);
                        
                        if(filtrados.length == 0) return;
                            
                        let doctype = filtrados[0].documentation_type_id;
                        //console.log(doc)
                        emolumentos_doc.append('<option value="' + article.id_article + ',' + article.money + ',' + doctype +'">' + article.nome + '</option>');

                        }
                    );

                } else if (data["type"] == 4) {

                    // Anulação de Matrículas

                    data["anulacao"].forEach(function(article) {

                        var doc = article.nome.toLowerCase();
                        //filtragem do id fornecido com o id da tabela article_documents!
                        const code = @json($code);
                        let filtro = article.id_article;
                        let filtrados = code.filter(item => item.article_id === filtro);
                        
                        if(filtrados.length == 0) return;
                        
                        let doctype = filtrados[0].documentation_type_id;
                       emolumentos_doc.append('<option value="' + article.id_article + ',' + article.money + ',' + doctype +'">' + article.nome + '</option>');
                                
                    });



                } else if (data["type"] == 3) {

                    // Diploma

                    data["diploma"].forEach(function(article) {

                        var doc = article.nome.toLowerCase();
                        //filtragem do id fornecido com o id da tabela article_documents!
                        const code = @json($code);
                        let filtro = article.id_article;
                        let filtrados = code.filter(item => item.article_id === filtro);
                        
                        if(filtrados.length == 0) return;
                        
                        let doctype = filtrados[0].documentation_type_id;
                        emolumentos_doc.append('<option value="' + article.id_article + ',' + article.money + ',' + doctype +'">' + article.nome + '</option>');
                         
                    });



                }else if (data["type"] == 5) {
                   
                    // Percurso académico

                    data["percurso"].forEach(function(article) {

                        var ref_id = article.id_code_dev;
                        const code = @json($code);
                        let filtro = article.id_article;
                        let filtrados = code.filter(item => item.article_id === filtro);
                        
                        if(filtrados.length == 0) return;
                            
                        let doctype = filtrados[0].documentation_type_id;

                        if(ref_id == 25){
                            emolumentos_doc.append('<option value="' + article.id_article + ',' + article.money + ',' + doctype +'">' + article.nome + '</option>');
                                
                        }
                                
                    });



                }
                else {
                    // Certificado

                    data["articles"].forEach(function(article) {

                        var doc = article.nome.toLowerCase();

                        //filtragem do id fornecido com o id da tabela article_documents!
                        const code = @json($code);
                        let filtro = article.id_article;
                        let filtrados = code.filter(item => item.article_id === filtro);
                        
                        if(filtrados.length == 0) return;
                        
                        let doctype = filtrados[0].documentation_type_id;
                        //console.log(doc)
                        emolumentos_doc.append('<option value="' + article.id_article + ',' + article.money + ',' + doctype +'">' + article.nome + '</option>');
                    
                    });

                }


                emolumentos_doc.selectpicker('refresh');
                emo.show();
                student_list.show();

                getUser();

            });
        }

        function getUser() {

          var tipo = $('#emolumentos_doc').val();
          var doc_type = 0;
          if(tipo != null){
         
           tipo = tipo.split(",");
            console.log("DEBUG tipo array:", tipo);
            doc_type = tipo[2];
            console.log("DEBUG tipo[2]:", doc_type);


          }
            $.ajax({
                url: "/avaliations/matriculation_requerimento/" + ano_lectivo.val()+ "," + doc_type,
                type: "GET",
                data: { _token: '{{ csrf_token() }}'},
                cache: false,
                dataType: 'json',
            }).done(function(data) {
                student.empty();
                if(data["doc_type"] == 9 || data["doc_type"] == 12 || data["doc_type"] == 4 || data["doc_type"] == 5 || data["doc_type"] == 6){
                    data["matriculation"].forEach(function(user) {
                        student.append('<option value="' + user.codigo + '">' + user.name + ' #' + user.code + ' ( ' + user.email +' )</option>');
                    }); 
                }else{
                    student.empty();
                    data["matriculation"].forEach(function(user) {
                        student.append('<option value="' + user.codigo + '">' + user.name + ' #' + user.matricula + ' ( ' + user.email +' )</option>');
                    }); 
                } 
                validar();
                student.selectpicker('refresh');
            });
        }

        function store_doc() {

            var ano = 0;
            var efeitos = 0;
            var datafinal = 0;
            var folha = 0;
            var registro = 0;

            if (student_year.val() == null) {
                anos = " ";
            } else {
                anos = student_year.val();
            }
            if (efeito.val() == "") {
                efeitos = " ";
            } else {
                efeitos = efeito.val();
            }
            if (dataconclusao.val() == "") {
                datafinal = " ";
            } else {
                datafinal = dataconclusao.val();
            }
            if (folhaN.val() == "") {
                folha = " ";
            } else {
                folha = folhaN.val();
            }
            if (data_outorga.val() == "") {
                dataoutorga = " ";
            } else {
                dataoutorga = data_outorga.val();
            }
             if (n_registro.val() == "") {
                registro = " ";
            } else {
                registro = n_registro.val();
            }
           
            $.ajax({
                url: "/avaliations/store_doc/" + [ano_lectivo.val(), emolumentos_doc.val(), student.val(),
                    anos, efeitos,datafinal,folha,dataoutorga,registro
                ],
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {


                if (data["code"] == "exist") {
                    alert.show();
                    title.text(" Atenção!");
                    sms.html(data["dados"]);

                    alert.removeClass("alert-danger");
                    alert.removeClass("alert-success");
                    alert.addClass("alert-warning");
                }
                if (data["code"] == "success") {
                    alert.show();
                    title.text(" Sucesso!");
                    sms.html(data["dados"]);

                    alert.removeClass("alert-danger");
                    alert.removeClass("alert-warning");
                    alert.addClass("alert-success");
                }
                if (data["code"] == "empty") {
                    alert.show();
                    title.text(" Atenção!");
                    sms.html(data["dados"]);

                    alert.removeClass("alert-warning");
                    alert.removeClass("alert-success");
                    alert.addClass("alert-danger");
                }
                setTimeout(function() {
                    alert.hide();
                }, 3000);
            });
        }

        // Requerer documento

        btn_requerer.click(function() {
            
            store_doc();
        });
        // Validar se o estudante e o emolumento foram seleccionados
        // e mostrar o botão de requerer
        
        function validar() {

            if (emolumentos_doc.val() == null || student.val() == null) {
                btn_requerer.hide();
            } else {
                btn_requerer.show();
            }
        }
    </script>
@endsection
