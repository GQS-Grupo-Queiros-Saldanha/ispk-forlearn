@section('title',__('Visualizar Pauta da Disciplina'))
{{-- #e9ecef --}}

@extends('layouts.backoffice')
 
@section('content')
<style>
    #lista_tr tr>td{
        border: black 0.9px solid;
    }
    .listaMenu{
        background: #e8ecef;
    }
    .listaMenu td{
        border: black 0.8px solid;
        font-size: 0.9pc;
    }
</style>
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            Exibir Pauta de Exame da Disciplina
                        </h1>
                    </div>
                    <div class="col-sm-6">               
                        <div class="float-right mr-4" style="width:400px; !important">
                            <label>Selecione o ano lectivo</label>
                            <select  name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
                              @foreach ($lectiveYears as $lectiveYear)
                                    @if ($lectiveYearSelected == $lectiveYear->id)
                                        <option style="width: 100%;" value="{{ $lectiveYear->id }}" selected>
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                            {{-- {{ Form::bsLiveSelectEmpty('lectiveYear->id', [], null, ['id' => 'lectiveYear->id', 'class' => 'form-control','disabled'])}}      --}}
                                        </option>
                                    @else
                                        <option style="width: 100%;" value="{{ $lectiveYear->id }}">
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
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                {!! Form::open(['route' => ['store_final_grade']]) !!}

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
                        <!--
                        
                        <button type="submit" class="btn btn-success mb-3">
                            <i class="fas fa-plus-circle"></i>
                              Salvar
                        </button>
                        --->

                        <div class="card">                            
                            <div class="row" >
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Selecione o curso</label>
                                        <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="curso_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina" tabindex="-98">
                
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Selecione a turma</label>
                                        <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="turma" tabindex="-98">                                                    
                                                        
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" >
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Selecione a disciplina</label>
                                        <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina" tabindex="-98">
                
                                        </select>
                                    </div>
                                </div>
                            </div> 
                        </div>

                        <input type="hidden" value="0" id="verificarSelector">
                        <hr>

                        <div class="card">
                            <div class="float-right">
                                <div class="row">
                                    <div class="col-8">
                                        <a class="btn btn-primary" id="generate-pdf" target="_blank">
                                            IMPRIMIR
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        
                        <div class="card mr-1" id="pauta_disciplina">
                            <h4 id="titulo_semestre"></h4>
                            <table class="table">
                                <thead class="listaMenu" >
                                    <tr id="listaMenu">
                                        
                                    </tr>
                                </thead>
                                <tbody id="lista_tr">
                                    
                                </tbody>
                            </table>    
                            
                            {{-- <table class="table">
                                <thead class="" >
                                    <tr>
                                        <td>Legenda de resultados válidos</td>
                                        <td style="padding-left: 250px">Totais</td> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>0-20 (sem decimais)</td>
                                        <td style="padding-right: 300px">Classificação de componente</td>
                                        <td>Estudante</td>
                                    </tr>
                                    <tr>
                                        <td>D</td>
                                        <td style="padding-right: 300px">Desistiu da prova</td>
                                    </tr>
                                    <tr>
                                        <td>F</td>
                                        <td style="padding-right: 300px">Faltou á prova</td>
                                    </tr>
                                    <tr>
                                        <td>S</td>
                                        <td style="padding-right: 300px">Sem frequência à disciplina</td>
                                    </tr>
                                    <tr>
                                        <td>R</td>
                                        <td style="padding-right: 500px">Reprovado</td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td style="padding-right: 500px">Aluno em situação irregular</td>
                                    </tr>
                                        <td>**</td>
                                        <td style="padding-right: 500px">Aluno no estado Suspenção Administrativa</td>
                                    </tr>
                                </tbody>
                            </table> --}}
                        </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function (){
            var id_anoLectivo=$("#lective_year");
            var anoCurso_id_Select=$("#anoCurso_id_Select")
            var curso=$("#curso_id_Select")
            var Disciplina_Select=$("#Disciplina_id_Select")
            var Turma_id_Select=$("#Turma_id_Select")
            var disciplina_nome;
            var turma_nome;
            var curso_nome;
            var ano_nome;
            var disciplina_regime;
            
            document.getElementById('generate-pdf').style.visibility = 'hidden';
            
            getCurso(id_anoLectivo);
            ano_nome = $("#lective_year")[0].selectedOptions[0].text;

            //Remoção de caracters;
            ano_str1 = $("#lective_year")[0].selectedOptions[0].text[3];
            ano_str2 = ano_str1.concat($("#lective_year")[0].selectedOptions[0].text[4]);
            
            var id_curso  =  ano_nome.replace(ano_str2,'');
            id_curso  =  id_curso.replace('/','');
            var ano_str1;
            var ano_str2;
            var ano_valor = "20";
            
            ano_str2 = ano_valor.concat(id_curso);

            id_anoLectivo.bind('change keypress',function(){
                $("#lista_tr").empty();
                $("#listaMenu").empty();
                Turma_id_Select.empty();     
                Disciplina_Select.empty();

                Disciplina_Select.prop('disabled', true);
                Turma_id_Select.prop('disabled', true);
                Turma_id_Select.selectpicker('refresh');   
                Disciplina_Select.selectpicker('refresh');   
            })

            function getCurso(id_anoLectivo) {
                $.ajax({
                    url: "/avaliations/getCurso/"+id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function (data){                   
                    if (data['data'].length>0) { 
                        $("#lista_tr").empty(); 
                        $("#listaMenu").empty();                         
                        curso.empty();
                        
                        curso.append('<option selected="" value="0">Selecione o curso</option>');
                        $.each(data['data'], function (indexInArray, row) { 
                            curso.append('<option value="'+ row.id+','+ row.duration_value +' ,'+ row.code +' ">' + row.nome_curso + '</option>');
                        }); 
                        curso.prop('disabled', false);
                        curso.selectpicker('refresh');
                    }                    
                });                    
            }

                
            curso.bind('change keypress',function() {
                var vetorCurso= curso.val();
                var re = /\s*,\s*/;
                var id_curso  = vetorCurso.split(re);
                $.ajax({
                    url: "/avaliations/getTurma/"+id_anoLectivo.val()+"/"+id_curso[0],
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function (data){
                        
                    curso_nome = $("#curso_id_Select")[0].selectedOptions[0].text; 
                    
                    if(data==500){
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        alert("Atenção! este curso não está associada a nenhuma turma no ano lectivo selecionado, verifique a edição de plano de estudo do mesma.");
                    } 

                    if (data['data'].length>0) {
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        Turma_id_Select.empty();     
                        Turma_id_Select.append('<option selected="" value="0">Selecione a turma</option>');

                        Disciplina_Select.prop('disabled', true);
                        $.each(data['data'], function (indexInArray, row) { 
                            Turma_id_Select.append('<option value="'+ row.id+' ,'+ row.year+'">'+row.display_name+'</option>');
                        });

                        Turma_id_Select.prop('disabled', false);
                        Turma_id_Select.selectpicker('refresh');    
                    }                                                                              
                });
            })

            Turma_id_Select.bind('change keypress', function() {
                turma_nome = $("#Turma_id_Select")[0].selectedOptions[0].text;
                
                getDiscipline()
            })

            function getDiscipline() {
                var vetorCurso= curso.val();
                var re = /\s*,\s*/;
                var arrayCurso  = vetorCurso.split(re);

                var vetorTurma= Turma_id_Select.val();
                var reTurma = /\s*,\s*/;
                var anoCursoturma  = vetorTurma.split(reTurma); 

                $.ajax({
                    url: "/avaliations/getDiscipline/"+id_anoLectivo.val()+"/"+anoCursoturma[1]+"/"+arrayCurso[0],
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function (data){
                    $("#lista_tr").empty();
                    $("#listaMenu").empty();

                    Disciplina_Select.empty();     
                    Disciplina_Select.append('<option selected="" value="0">Selecione a disciplina</option>');

                    if (data['data'].length>0) {
                        $.each(data['data'], function (indexInArray, row) { 
                            Disciplina_Select.append('<option value="'+ row.id_disciplina+','+ row.periodo_disciplina+'">' + row.code + '  ' + row.dt_display_name + '</option>');
                        });
                    }

                    Disciplina_Select.prop('disabled', false);
                    Disciplina_Select.selectpicker('refresh');                                                                
                });
            } 

            Disciplina_Select.bind('change keypress', function() {                    
                var vetorCurso= curso.val();
                var re = /\s*,\s*/;
                var id_curso  = vetorCurso.split(re);

                var  vetorDisciplina= Disciplina_Select.val();
                var vetor = /\s*,\s*/;
                var id_disciplinaVetor  = vetorDisciplina.split(vetor);
                
                disciplina_nome = $("#Disciplina_id_Select")[0].selectedOptions[0].text;

                $.ajax({
                    url: "/avaliations/getMenuAvaliacoesDisciplina/"+Turma_id_Select.val()+"/"+id_anoLectivo.val()+"/"+id_curso[0]+"/"+id_disciplinaVetor[0]+"/"+anoCurso_id_Select.val()+"/"+id_disciplinaVetor[1],
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function (data){
                    // console.log(data);
                    var tabelatr=""
                        
                    $("#lista_tr").empty();
                    $("#listaMenu").empty();

                    if(data==500){
                        $("#lista_tr").empty();
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        alert("Atenção! este curso não está associada a nenhuma Prova no ano lectivo selecionado.");
                    } 
                                                                       
                    document.getElementById('generate-pdf').style.visibility = 'hidden';
                    getStudentNotasPautaFinal()                                
                });
            })
               
            function getStudentNotasPautaFinal() {  
                var vetorCurso = curso.val();
                var re = /\s*,\s*/;
                var id_curso = vetorCurso.split(re);

                var vetorDisciplina = Disciplina_Select.val();
                var vetor = /\s*,\s*/;
                var id_disciplinaVetor  = vetorDisciplina.split(vetor);               

                console.log("ANO: ",id_anoLectivo.val(), "CURSO: ", id_curso[0], "TURMA: ", Turma_id_Select.val(), "DISCIPLINA: ", id_disciplinaVetor[0])

                $.ajax({
                    url: "/avaliations/getStudentNotasPautaFinal/"+id_anoLectivo.val()+"/"+id_curso[0]+"/"+Turma_id_Select.val()+"/"+id_disciplinaVetor[0],
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done( function (data)
                {     
                    // var element = document.getElementById("generate-pdf");
                    // element.href = "";
                    // console.dir(data['data']['alunos_notas']);

                    disciplina_regime = data['data']['periodo_disc'][0].value_disc;
                        if (typeof data['data']['exame'] !== 'undefined') {
                            if (data['data']['exame'].length > 0 && data['data']['alunos_notas'].length > 0){
                                //GERADOR NO MENU PAUTA
                                $("#listaMenu").empty();
                                tabelatr+="<td>Nº Aluno</td>"
                                tabelatr+="<td>Nome</td>"
                                tabelatr+="<td>PF1</td>"
                                tabelatr+="<td>PF2</td>"
                                tabelatr+="<td>OA</td>"
                                tabelatr+="<td>MAC</td>"
                                tabelatr+="<td>NEEN</td>"
                                tabelatr+="<td>Época Nornal<br>(Exame)</td>"
                                tabelatr+="<td>Observações</td>"
                                $("#listaMenu").append(tabelatr); 
                                
                                document.getElementById('generate-pdf').style.visibility = 'visible';
                            }
                            else {
                                $("#listaMenu").empty();
                                tabelatr+="<td class='text-center'>A pauta está indisponivél ...</td>"
                                $("#listaMenu").append(tabelatr); 
                            }
                        } 
                        else {
                            $("#listaMenu").empty();
                            tabelatr+="<td class='text-center'>A pauta está indisponivél ...</td>"
                            $("#listaMenu").append(tabelatr); 
                        }
                    
                    // console.log("DADOS:", data['data']['exame'][0].has_mandatory_exam); 
                    var numero_alunos = 0;
                    var pf1_percentagem = 0;
                    var pf2_percentagem = 0;
                    var oa_percentagem = 0;

                    var tabelatr="";
                    var resultados_student=data['data']['dados']                       
                    
                    //GERA A LISTA DE ESTUDANTES E SUAS NOTAS
                    $("#lista_tr").empty(); 
                        
                    if (data['data']['exame'].length > 0) {
                        //Estrutura de repitição que lista os alunos
                        $.each(resultados_student, function (index, item) { 
                            numero_alunos += 1;
                            pf1 = 0;
                            pf2 = 0;
                            oa = 0;
                            neen = 0;
                            exame_pauta = 0;
                            j = 0;
                            calculo_mac = 0;
                            tabelatr+="<tr><td>"+numero_alunos+"</td>"
                            tabelatr+="<td>"+index+"</td>"

                            $.each(item, function (index_avaliacao, item_avaliacao) {
                                // Estrura de repetição que pega as metricas PF1.
                                if (item_avaliacao.Metrica_nome === "PF1") {
                                    if (item_avaliacao.nota_anluno === null) {
                                        tabelatr+="<td>"+'F'+"</td>"
                                    }
                                    else {
                                        pf1_percentagem = item_avaliacao.percentagem_metrica / 100;
                                        pf1 = item_avaliacao.nota_anluno;
                                        tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                    }
                                    j += 1;
                                }
                                
                                // Estrura de repetição que pega as metricas PF2.
                                if (item_avaliacao.Metrica_nome === "PF2") {
                                    if (item_avaliacao.nota_anluno === null) {
                                        tabelatr+="<td>"+'F'+"</td>"
                                    }
                                    else {
                                        pf2_percentagem = item_avaliacao.percentagem_metrica / 100;
                                        pf2 = item_avaliacao.nota_anluno;
                                        tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                    }
                                    j += 1;
                                }

                                // Estrura de repetição que pega as metricas OA..
                                if (item_avaliacao.Metrica_nome === "OA") {
                                    if (item_avaliacao.nota_anluno === null) {
                                        tabelatr+="<td>"+'F'+"</td>"
                                    }
                                    else {
                                        oa_percentagem = item_avaliacao.percentagem_metrica / 100;
                                        oa = item_avaliacao.nota_anluno;
                                        tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                    }
                                    j += 1;
                                }
                                // Estrura de repetição que pega as metricas OA..
                                if (item_avaliacao.Metrica_nome === "Neen") {
                                    if (item_avaliacao.nota_anluno === null) {
                                        // tabelatr+="<td>"+'F'+"</td>"
                                        neen = 0;
                                    }
                                    else {
                                        neen = item_avaliacao.nota_anluno;
                                    }
                                }
                            });

                            //Fim das avaliações PF1, PF2, OA
                            if (j >= 2) {                                
                                if (j >= 3) {
                                    // tabelatr+="<td>"+"</td>"
                                }
                                else {
                                    tabelatr+="<td>"+1+"</td>"
                                }
                                //Calculo da MAC
                                calculo_mac = (((pf1 * pf1_percentagem) + (pf2 * pf2_percentagem) + (oa * oa_percentagem)) / (pf1_percentagem + pf2_percentagem + oa_percentagem));                                
                                exame_pauta = (parseInt(calculo_mac) + parseInt(neen)) / parseInt(2);
                                tabelatr+="<td>"+Math.round(calculo_mac)+"</td>" 
                                // console.log(data['data']['exame'][0].has_mandatory_exam);
                                // console.log("MAC",calculo_mac, "NEEN",neen, "EXAME",exame_pauta, "CAL", parseInt((calculo_mac + neen) / 2));  
                                
                                // Verifica se a disciplina têm EXAME obrigatório
                                if (data['data']['exame'][0].has_mandatory_exam == 1){
                                    //Mostra a nota do NEEN
                                    tabelatr+="<td>"+Math.round(neen)+"</td>"
                                    
                                    // Mostra o Resultado da Pauta
                                    if (exame_pauta >= 10) {                
                                        tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                        tabelatr+="<td>"+'Aprovado'+"</td>"
                                    }
                                    if (exame_pauta >= 7 && exame_pauta < 10) {                
                                        tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                        tabelatr+="<td>"+'Reprovado'+"</td>"
                                    }
                                    if (exame_pauta < 7) {                
                                        tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                        tabelatr+="<td>"+'Recurso'+"</td>"
                                    }
                                }
                                else {
                                    // Caso não tenha EXAME, verifica o MAC
                                    if (calculo_mac < 7) {
                                        //Mostra a nota do NEEN
                                        tabelatr+="<td> </td>"
                                        // Mostra a nota do EXAME Normal
                                        tabelatr+="<td>"+Math.round(calculo_mac)+"</td>"
                                        tabelatr+="<td>"+'Recurso'+"</td>"
                                    } 
                                    //Validação da nota do NEEN                                   
                                    if (Math.round(calculo_mac) >= 14) {
                                        //Mostra a nota do NEEN
                                        // tabelatr+="<td>"+Math.round(neen)+"</td>"
                                        tabelatr+="<td> </td>"
                                        // Mostra a nota do EXAME Normal                                           
                                        tabelatr+="<td>"+Math.round(calculo_mac)+"</td>"
                                        tabelatr+="<td>"+'Aprovado'+"</td>"
                                    }

                                    // Mostra o Resultado da Pauta
                                    if (Math.round(calculo_mac) >= 7 && Math.round(calculo_mac) < 14) {
                                        //Mostra a nota do NEEN
                                        tabelatr+="<td>"+Math.round(neen)+"</td>"
                                        // tabelatr+="<td> </td>"
                                        if (Math.round(exame_pauta) >= 10) {
                                            // Mostra a nota do EXAME Normal
                                            // console.log((parseInt(calculo_mac) + parseInt(neen)) / parseInt(2), exame_pauta);
                                            tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                            tabelatr+="<td>"+'Aprovado'+"</td>"
                                        }
                                        else {
                                            // Mostra a nota do EXAME Normal
                                            // console.log((parseInt(calculo_mac) + parseInt(neen)) / parseInt(2), exame_pauta);
                                            tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                            tabelatr+="<td>"+'Reprovado'+"</td>"
                                        }
                                    }
                                }                               
                            }
                        });
                        
                        //Tag que fecha a tabela
                        tabelatr+="</tr>"
                        $("#lista_tr").append(tabelatr);
                        
                        
                        document.getElementById('generate-pdf').onclick = function() {
                            var conteudo = document.getElementById('pauta_disciplina').innerHTML,
                                tela_impressao = window.open('about:blank');

                            //Formatação do documento
                            tela_impressao.document.write(' <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500|Roboto+Slab:400,700|MontSerrat:300,400,500,600,700,800&display=swap">');
                            tela_impressao.document.write('<link rel="stylesheet" href="{{ asset('css/vendor.css') }}">');
                            tela_impressao.document.write('<link rel="stylesheet" href="{{ asset('css/backoffice/bootstrap-select.min.css') }}">');
                            tela_impressao.document.write('<style>#lista_tr tr>td{border: black 0.9px solid;}.listaMenu{background: #e8ecef;} .listaMenu td{border: black 0.8px solid;font-weight: bold; font-size: 0.9pc;}</style>');

                            tela_impressao.document.write('<stylemargin-top: 5px;');
                            tela_impressao.document.write('margin-left: 5px:');
                            tela_impressao.document.write('margin-bottom: 10px;');
                            tela_impressao.document.write('margin-right: 5px; </style');

                            //Cabeçalho do documento
                            tela_impressao.document.write('<p><b>Pauta de '+disciplina_nome);
                            tela_impressao.document.write('<br> Licenciatura em '+curso_nome+'</b></p>');
                            tela_impressao.document.write('<br><b>Código: </b>'+disciplina_nome);
                            tela_impressao.document.write('<br><b>Ano Curricular: </b>'+data['data']['alunos_notas'][0].ano_curricular);
                            tela_impressao.document.write('<br><b>Ano Lectivo: </b>'+ano_str2);
                            tela_impressao.document.write('<br><b>Regime: </b>'+disciplina_regime);
                            tela_impressao.document.write('<br><b>Docente: </b>'+data['data']['professor']);
                            tela_impressao.document.write('<br><b>Turma: </b>'+turma_nome+'</p>');
                            
                            tela_impressao.document.write(conteudo);
                            tela_impressao.window.print();
                        }
                    };                        
                }); 
            }      
        })
     
    </script>
@endsection

