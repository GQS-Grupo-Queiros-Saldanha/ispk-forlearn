{{-- 
    Módulo Avaliação - 05/04/2022
    Zacarias Juliano
    Modificação do Arquivo Original "discipline_studentes_grades"
--}}

@section('title',__('Visualizar Pauta de Recurso'))
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
                        <h1 class="m-0 text-dark" id="pauta_titulo">
                            Exibir Pauta de Recurso
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

        {{-- INCLUI O MENU DE BOTÕES --}}
        @include('Avaliations::avaliacao.show-panel-avaliation-button', ['pauta' => 3])  

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">                
                
                {!! Form::open(['route' => ['publisher_final_grade'],'id'=>"publishForm"]) !!}                
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
                                            <label>Selecione o curso</label>
                                            <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="curso_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina" tabindex="-98">
                    
                                            </select>                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecione a turma</label>
                                            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="i_turma" tabindex="-98">                                                    
                                                            
                                            </select>                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecione a disciplina</label>
                                            <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="i_disciplina" tabindex="-98">
                    
                                            </select>                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" value="0" id="verificarSelector">
                            <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="">
                            {{-- {{Dados}} --}}
                            <input type="hidden" id="data_html" name="data_html" value="">
                            {{-- {{Dados Curso}} --}}
                            <input type="hidden" id="curso_id" name="curso_id" value="">
                            {{-- {{Dados Turma}} --}}
                            <input type="hidden" id="id_turma" name="id_turma" value="">
                            {{-- {{Dados Disciplina}} --}}
                            <input type="hidden" id="id_disciplina" name="id_disciplina" value="">
                            {{-- {{Dados Pauta Code}} --}}
                            <input type="hidden" id="pauta_code" name="pauta_code" value="">  
                            <input type="hidden" id="pauta_dados" name="pauta_dados" value="">
                            <hr>

                            <div class="card">
                                <div class="float-right">
                                    <div class="row">
                                        <div class="col-8">
                                            <a class="btn btn-primary" id="generate-pdf" target="_blank">
                                                IMPRIMIR
                                            </a>                                        
                                            
                                            {{-- @if(auth()->user()->hasAnyRole(['teacher'])) --}}
                                                {{-- <button type="button" class="btn btn-success" style="left: 10.5em;" id="togglee">
                                                    <i class="fas fa-lock" id="icone_publish"></i>
                                                        Publicar Pauta
                                                </button> --}}
                                            {{-- @endif --}}
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            
                            <div class="card mr-1" id="pauta_disciplina">
                                <h4 id="titulo_semestre"></h4>
                                <table class="table_pauta">
                                    <thead class="table_pauta" >
                                        <tr id="listaMenu">
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="lista_tr">
                                        
                                    </tbody>
                                </table>  
                            </div>

                        </div>
                    </div> 
                {!! Form::close() !!}
            </div>
        </div>

        <!-- Modal  de confirmação de cadastro de notas-->
        <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                
                    <div class="modal-header bg-danger text-light">
                        <h5 class="modal-title" id="exampleModalLabel">ALERTA | Confirmação de dados</h5>
                        
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="float-right">
                            <p class="text-danger" style="font-weight:bold; !important"> Caro Coordenador(a) ({{auth()->user()->name}}), a acção de <label id="acaoID" class="text-danger" style="font-weight:bold; !important"></label> pauta é de sua inteira responsabilidade
                            <br></p>
                        </div>

                        <br>
                        <br>

                        <div style="margin-top:50px; !important">
                        <p style="padding:5px; !important" id="idTExto"></p>

                            <ul>
                                <li style="padding:5px; !important"  id="text1">
                                </li>
                                <li style="padding:5px; !important" id="text2">
                                </li>

                                <li style="padding:5px; !important" id="text3">  
                                </li>
                            </ul>
                        </div>

                        <div style="margin-top:10px; !important" id="confirmMessage">
                            <p>
                            No caso de <span class="text-danger"><b>HAVER</b></span> alguma das situações acima assinaladas, por favor seleccione: Contactar os gestores forLEARN pessoalmente.
                            </p>
                            <br>
                            <p>
                            No caso de <span class="text-success"><b>NÃO HAVER</b></span> nenhuma situação acima, por favor seleccione: Tenho a certeza.
                            </p>
                        </div>

                    </div>
                    <div class="modal-footer" >
                        <button type="button" class="btn btn-success" data-dismiss="modal">Contactar gestores forLEARN</button>
                        <nav id="ocultar_btn">
                            <button type="button" class="btn btn-danger" id="btn-PublishSubmit">Tenho a certeza</button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function (){
            console.clear();
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
            var selector_pauta = null;
            
            selector_pauta = $("#selector_pauta").parent().find('input').is(':checked');

            function ouculta_notao() {
                // document.getElementById('togglee').style.visibility = 'hidden';
                document.getElementById('generate-pdf').style.visibility = 'hidden';
            }
                    
            $("#selector_pauta").click(function() { 
                $("#selector_pauta").submit(); 
                selector_pauta = $("#selector_pauta").parent().find('input').is(':checked');
                getCurso(id_anoLectivo);
                change_title_page(selector_pauta);
            });

            change_title_page($("#selector_pauta").parent().find('input').is(':checked'));

            function change_title_page(estado) {
                if (estado === false) {
                    $("#pauta_titulo").parent().find('h1').html("PAUTA DE RECURSO - Proprinas Liquidadas");
                }
                else {
                    $("#pauta_titulo").parent().find('h1').html("PAUTA DE RECURSO - Proprinas Pedentes");
                }
            }
            
            ouculta_notao();
            
            getCurso(id_anoLectivo);
            ano_nome = $("#lective_year")[0].selectedOptions[0].text;

            // DADOS LECTIVO
            $("#id_anoLectivo").val(id_anoLectivo.val());

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
                ouculta_notao();
                
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
                ouculta_notao();

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
                ouculta_notao();

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

                ouculta_notao();
                
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

                // DADOS CURSO                
                $("#curso_id").val(id_curso[0]);
                $("#id_disciplina").val(id_disciplinaVetor[0]);
                $("#id_turma").val(Turma_id_Select.val());
                
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
                                                                       
                    ouculta_notao();
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

                // console.log("ANO: ",id_anoLectivo.val(), "CURSO: ", id_curso[0], "TURMA: ", Turma_id_Select.val(), "DISCIPLINA: ", id_disciplinaVetor[0])

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

                    // CHAMA O MODAL
                    $("#togglee").click(function(){
                        $("#exampleModal").modal('show');
                    });
                    // PUBLICA A PAUTA
                    $("#btn-PublishSubmit").click(function(){
                        $("#publishForm").submit();
                    });

                    // GET THE DE DAY OF MONTH
                    const mes_day = new Date().getDate();

                    // GET THE NUMBER OF MONTH
                    const currentMonth = new Date().getMonth() + 1; 

                    // IF THE DAY OF MONTH IS GRETER THAM 15, PLUS 2         
                    const mes_aval = new Date();
                    if (mes_day < 15) {                                         
                        mes_aval.setMonth(currentMonth - (data['data']['validacao_proprina']['quantidade_mes'] + 1));
                    }
                    else {                            
                        mes_aval.setMonth(currentMonth - (data['data']['validacao_proprina']['quantidade_mes'] + 2));
                    }

                    // GET THE NAME OF MONTH 
                    const mes_propina = mes_aval.toLocaleString("pt-PT", {month: "long"}); 
                    if (mes_propina === "março"){
                        var mes = "mes_"+"marco";
                    }
                    else {
                        var mes = "mes_"+mes_propina;
                    }

                    // $("#selector_pauta").click(function() {  
                    //     console.log(123);
                    // });

                    // console.log( currentMonth, mes_propina, selector_pauta);

                    disciplina_regime = data['data']['periodo_disc'][0].value_disc;
                        if (typeof data['data']['exame'] !== 'undefined') {
                            if (data['data']['exame'].length > 0 && data['data']['alunos_notas'].length > 0){
                                //GERADOR NO MENU PAUTA
                                $("#listaMenu").empty();
                                tabelatr+="<td>Nº Aluno</td>"
                                tabelatr+="<td>Nome</td>"
                                // tabelatr+="<td>PF1</td>"
                                // tabelatr+="<td>PF2</td>"
                                // tabelatr+="<td>OA</td>"
                                // tabelatr+="<td>MAC</td>"
                                tabelatr+="<td>Recurso</td>"
                                // tabelatr+="<td>Época Nornal<br>(Exame)</td>"
                                tabelatr+="<td>Observações</td>"
                                $("#listaMenu").append(tabelatr);
                                
                                // Mostra os botões
                                if(data['data']['estado_pauta']==1){
                                    $("#togglee").text("Desbloquear Pauta");
                                    
                                    //no modal de alerta de publicação de notas
                                    $("#acaoID").text("Desbloquear");
                                    $("#idTExto").text("Após desbloquear a pauta, algumas acções podem ser realizadas, nomeadamente:");
                                    $("#text1").text("As notas poderam ser EDITADAS");
                                    $("#text2").text("A pauta gerada anteriormente será DESCARTADA");
                                    $("#text3").text("A pauta deixará de estar DISPONÍVEL");
                                    $("#confirmMessage").hide();
                                    //fim modal

                                    $("#togglee").removeClass("btn-success");
                                    $("#togglee").addClass("btn-warning text-dark");

                                    $("#icone_publish").removeClass("fas fa-lock");
                                    $("#icone_publish").addClass("fas fa-unlock");
                                }
                                else if(data['data']['estado_pauta']==0){
                                    $("#togglee").text("");
                                    $("#togglee").text("Publicar Pauta");

                                     //no modal de alerta de publicação de notas
                                    $("#acaoID").text("Publicar");

                                    $("#idTExto").text("Verifique se os dados da pauta estão correctos, nomeadamente: ");
                                    $("#text1").text("Todos os alunos pertencem a esta TURMA?");
                                    $("#text2").text("Falta algum aluno nesta TURMA?");
                                    $("#text3").text("Há alguma anomalia nos cálculos das NOTAS?");
                                    $("#confirmMessage").show();
                                    //fim modal

                                    $("#icone_publish").removeClass("fas fa-unlock ");
                                    $("#icone_publish").addClass("fas fa-lock");

                                    $("#togglee").addClass("btn-success");
                                    $("#togglee").removeClass(" btn-warning");
                                 
                                }else{
                                    $("#acaoID").text("Publicar");
                                    $("#idTExto").text("Verifique se os dados da pauta estão correctos, nomeadamente: ");
                                    $("#text1").text("Todos os alunos pertencem a esta TURMA?");
                                    $("#text2").text("Falta algum aluno nesta TURMA?");
                                    $("#text3").text("Há alguma anomalia nos cálculos das NOTAS?");

                                }                                 
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
                    // INFORMA O TIPO DE PAUTA A SER SALVA
                    $("#pauta_code").val(10);                    
                    // $("#pauta_dados").val(lista_alunos_notas);
                        
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

                            // MOSTRA OS ESTUDANTES COM BASE NA PROPINA PAGA
                            if (selector_pauta === false) {
                                // MOSTRA OS ESTUDANTES QUE PAGARAM AS PROPRINAS
                                $.each(data['data']['propinas']['original']['data'], function (index_proprina, item_proprina) { 
                                    if (index == item_proprina.student) {
                                        // VERIFICA SE A PROPINA ESTÁ PAGA
                                        if (item_proprina[mes].slice(0,4) === "PAGO") {

                                            tabelatr+="<tr><td>"+item[numero_alunos-1].code_matricula+"</td>"
                                            tabelatr+="<td>"+index+"</td>"

                                            // document.getElementById('togglee').style.visibility = 'visible';
                                            document.getElementById('generate-pdf').style.visibility = 'visible';
                                            
                                            // console.log(item[numero_alunos-1].code_matricula);

                                            $.each(item, function (index_avaliacao, item_avaliacao) {
                                                // Estrura de repetição que pega as metricas PF1.
                                                if (item_avaliacao.Metrica_nome === "PF1") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        // tabelatr+="<td>"+'F'+"</td>"
                                                    }
                                                    else {
                                                        pf1_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                        pf1 = item_avaliacao.nota_anluno;
                                                        // tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                                    }
                                                    j += 1;
                                                }
                                                
                                                // Estrura de repetição que pega as metricas PF2.
                                                if (item_avaliacao.Metrica_nome === "PF2") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        // tabelatr+="<td>"+'F'+"</td>"
                                                    }
                                                    else {
                                                        pf2_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                        pf2 = item_avaliacao.nota_anluno;
                                                        // tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.Metrica_nome === "OA") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        // tabelatr+="<td>"+'F'+"</td>"
                                                    }
                                                    else {
                                                        oa_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                        oa = item_avaliacao.nota_anluno;
                                                        // tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                                    }
                                                    j += 1;
                                                }
                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.Metrica_nome === "Neen") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        neen = 0;
                                                    }
                                                    else {
                                                        neen = item_avaliacao.nota_anluno;
                                                    }
                                                }
                                            });
                                            
                                            // console.log(j);
                                            //Fim das avaliações PF1, PF2, OA
                                            if (j >= 2) {                                
                                                
                                                //Calculo da MAC
                                                calculo_mac = (((pf1 * pf1_percentagem) + (pf2 * pf2_percentagem) + (oa * oa_percentagem)) / (pf1_percentagem + pf2_percentagem + oa_percentagem));                                
                                                exame_pauta = (parseInt(calculo_mac) + parseInt(neen)) / parseInt(2);
                                                // tabelatr+="<td>"+Math.round(calculo_mac)+"</td>"  
                                                
                                                //Mostra a nota do NEEN
                                                tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                
                                                // Mostra o Resultado da Pauta
                                                if (exame_pauta >= 10) {                
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Aprovado'+"</td>"
                                                }
                                                else {                                                       
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Reprovado'+"</td>"
                                                }                             
                                            }
                                            if (j <= 1) { 
                                                // 
                                                //Mostra a nota do NEEN
                                                tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                
                                                // Mostra o Resultado da Pauta
                                                if (exame_pauta >= 10) {                
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Aprovado'+"</td>"
                                                }
                                                else {                                                       
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Reprovado'+"</td>"
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                            else {
                                // MOSTRA OS ESTUDANTES QUE PAGARAM AS PROPRINAS
                                $.each(data['data']['propinas']['original']['data'], function (index_proprina, item_proprina) { 
                                    if (index == item_proprina.student) {
                                        // VERIFICA SE A PROPINA NÃO ESTÁ PAGA
                                        if (item_proprina[mes].slice(0,4) != "PAGO") {

                                            tabelatr+="<tr><td>"+item[numero_alunos-1].code_matricula+"</td>"
                                            tabelatr+="<td>"+index+"</td>"
                                            
                                            // document.getElementById('togglee').style.visibility = 'visible';
                                            document.getElementById('generate-pdf').style.visibility = 'visible';
                                            
                                            // console.log(item[numero_alunos-1].code_matricula);

                                            $.each(item, function (index_avaliacao, item_avaliacao) {
                                                // Estrura de repetição que pega as metricas PF1.
                                                if (item_avaliacao.Metrica_nome === "PF1") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        // tabelatr+="<td>"+'F'+"</td>"
                                                    }
                                                    else {
                                                        pf1_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                        pf1 = item_avaliacao.nota_anluno;
                                                        // tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                                    }
                                                    j += 1;
                                                }
                                                
                                                // Estrura de repetição que pega as metricas PF2.
                                                if (item_avaliacao.Metrica_nome === "PF2") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        // tabelatr+="<td>"+'F'+"</td>"
                                                    }
                                                    else {
                                                        pf2_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                        pf2 = item_avaliacao.nota_anluno;
                                                        // tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.Metrica_nome === "OA") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        // tabelatr+="<td>"+'F'+"</td>"
                                                    }
                                                    else {
                                                        oa_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                        oa = item_avaliacao.nota_anluno;
                                                        // tabelatr+="<td>"+item_avaliacao.nota_anluno+"</td>"
                                                    }
                                                    j += 1;
                                                }
                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.Metrica_nome === "Neen") {
                                                    if (item_avaliacao.nota_anluno === null) {
                                                        neen = 0;
                                                    }
                                                    else {
                                                        neen = item_avaliacao.nota_anluno;
                                                    }
                                                }
                                            });
                                            
                                            // console.log(j);
                                            //Fim das avaliações PF1, PF2, OA
                                            if (j >= 2) {                                
                                                
                                                //Calculo da MAC
                                                calculo_mac = (((pf1 * pf1_percentagem) + (pf2 * pf2_percentagem) + (oa * oa_percentagem)) / (pf1_percentagem + pf2_percentagem + oa_percentagem));                                
                                                exame_pauta = (parseInt(calculo_mac) + parseInt(neen)) / parseInt(2);
                                                // tabelatr+="<td>"+Math.round(calculo_mac)+"</td>"  
                                                
                                                //Mostra a nota do NEEN
                                                tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                
                                                // Mostra o Resultado da Pauta
                                                if (exame_pauta >= 10) {                
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Aprovado'+"</td>"
                                                }
                                                else {                                                       
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Reprovado'+"</td>"
                                                }                             
                                            }
                                            if (j <= 1) { 
                                                // 
                                                //Mostra a nota do NEEN
                                                tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                
                                                // Mostra o Resultado da Pauta
                                                if (exame_pauta >= 10) {                
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Aprovado'+"</td>"
                                                }
                                                else {                                                       
                                                    // tabelatr+="<td>"+Math.round(exame_pauta)+"</td>"
                                                    tabelatr+="<td>"+'Reprovado'+"</td>"
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        });
                        
                        //Tag que fecha a tabela
                        tabelatr+="</tr>"
                        $("#lista_tr").append(tabelatr);

                        // DADOS PAUTA
                        $("#data_html").val($("#pauta_disciplina").html()); 
                        
                        // IMPRIMI A PAUTA
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

