@section('title',__('Atribuír notas'))


@extends('layouts.backoffice')
    <style>
      table,
      th,
      td {
        padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;
      }
      .modal-backdrop { background-color: red; !important}
      #ConteudoMain{
          display: none;
      }
    </style>
@section('content')
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                         Atribuir Notas
                        </h1>
                    </div>
                    <div class="col-sm-6">
               
                        <div class="float-right mr-4" style="width:400px; !important">
                            <label>Selecione o ano lectivo</label>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
                                @foreach ($lectiveYears as $lectiveYear)
                                    @if ($lectiveYearSelected == $lectiveYear->id)
                                        <option style="width: 100%;" value="{{ $lectiveYear->id }}" selected>
                                            {{ $lectiveYear->currentTranslation->display_name }}
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

        {{--  Main content  --}}
        {{--  Main content  --}}


        <div class="content">


            <div class="container-fluid"
            >
                <div class="row">

                    <div class="col-md-12">
                      
                    </div>
                </div>













{{-- esta div é para imcorporar tudo, e depois acultar apois a seleção do ano lectivo--}}
<section id="ConteudoMain">




        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                {!! Form::open(['route' => ['avaliacao_aluno.store']]) !!}

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
{{-- 
                        <div hidden>
                            <button type="submit" class="btn btn-success mb-3 ml-3" id="btn-submit">
                                <i class="fas fa-plus-circle"></i>
                                Salvar
                            </button>  
                        </div> --}}

                        <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#exampleModal">
                            <i class="fas fa-plus-circle"></i>
                              Salvar
                        </button>
                            <div class="card">

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Edição de Plano de Estudo</label>
                                                {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'class' => 'form-control','disabled'])}}
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Disciplina</label>
                                            {{ Form::bsLiveSelectEmpty('discipline_id',[],null,['id' => 'discipline_id', 'class' => 'form-control', 'disabled'])}}
                                        </div>
                                    </div>

                                </div>

                                 <div class="row" id="discipline-group">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Avaliação</label>
                                            {{ Form::bsLiveSelectEmpty('avaliacao_id',[],null,['id' => 'avaliacao_id', 'class' => 'form-control', 'disabled'])}}
                                        </div>
                                    </div>
                                </div>

                                 <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Turma</label>
                                            {{ Form::bsLiveSelectEmpty('class_id',[],null,['id' => 'class_id', 'class' => 'form-control', 'disabled'])}}
                                        </div>
                                    </div>
                                </div>

                                 <div class="row" id="discipline-group">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Métrica</label>
                                            {{ Form::bsLiveSelectEmpty('metrica_id',[],null,['id' => 'metrica_id', 'class' => 'form-control', 'disabled'])}}
                                        </div>
                                    </div>
                                </div>






                            </div>
                            <hr>

                            <div class="card">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-hover">
                                           
                                            <thead>
                                                <th>#</th>
                                                <th class="text-center">Estado</th>
                                                <th>Nº Estudante</th>
                                                <th>Nome</th>
                                                <th>Nota</th>
                                            </thead>
                                           
                                            <tbody id="students">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>



{{--abaico fim da div geral de imcorporar e ocultar anos lectivos--}}
</section>
























{{-- esta section é aonde vai o proximos anos lectivos--}}
<section id="NextStap">
    <div >
            {{-- <h1>Novo formulário </h1> --}}
            <div class="content_s" style="margin-bottom: 10px">
                <div class="">
                    {{--Inicio do formulário--}}
                    <form action="">
                        
                        <div >
                            <button type="buttom" class="btn btn-success mb-3 ml-3" id="btn-Enviar" data-toggle="modal" data-target="#exampleModal">
                                <i class="fas fa-plus-circle"></i>
                                Salvar
                            </button>
                        </div>
                    
                      <div class="card" >

                        <div class="row" >
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Selecione a disciplina</label>
                                    <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="type_document" tabindex="-98">
           
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Selecione a turma</label>
                                    <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="type_document" tabindex="-98">
                                        <option value=""></option>          
                                                  
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row" style="display: none" id="caixaAvalicao">
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Selecione a avaliacão</label>
                                    <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="avaliacao_id_Select" data-actions-box="false" data-selected-text-format="values" name="type_document" tabindex="-98">
                                        <option value="">Selecione a avaliação </option>          
                                                  
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="display: none" id="caixaMatrica">
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Selecione a métrica</label>
                                    <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="metrica_id_Select" data-actions-box="false" data-selected-text-format="values" name="type_document" tabindex="-98">
                                        <option value="">Selecione a métrica </option>          
                                                  
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            {{-- <div class="col-6">
                                <div class="form-group col">
                                    <label>Selecione a Métrica</label>
                                    <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="Metrica_id_Select" data-actions-box="false" data-selected-text-format="values" name="type_document" tabindex="-98">
                                        <option value=""></option>          
                                                  
                                    </select>
                                </div>
                            </div> --}}
                        </div>
                        
                    

                    </div>
                </form>
                {{--Fim do formulario--}}

                <hr>
                {{--incio da tabela--}}
                <div id="tabela_new" style="display: none;">

                    
                    <div class="card  mr-2">
                    <div class="row">
                        <div class="col-12">
                            <h2 id="Titulo_Avalicao"></h2>
                            <table class="table table-hover ">
                                <thead>
                                    <th>#</th>
                                    <th class="text-center">Estado</th>
                                    <th>Nº Estudante</th>
                                    <th>Nome</th>
                                    <th>Nota</th>
                                </thead>
                                <tbody id="students_new">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>
                {{--fim da tabela--}}
                </div>
            </div>

     </div>

</section>




































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
                        <p class="text-danger" style="font-weight:bold; !important"> Caro DOCENTE ({{auth()->user()->name}}), as informações inseridas
                        neste<br> formulário ATRIBUIR NOTAS, são da sua inteira responsabilidade.
                        <br> Por favor seja rigoroso na informação prestada.</p>
                    </div>

                        <br>
                        <br>

                    <div style="margin-top:50px; !important">
                        <p style="padding:5px; !important">Verifique se os dados estão correctos, nomeadamente: </p>

                        <ul>
                            <li style="padding:5px; !important">
                                Todos os alunos pertencem a esta TURMA?
                            </li>
                            <li style="padding:5px; !important">
                                Falta algum aluno nesta TURMA?
                            </li>
                        </ul>
                    </div>

                   <div style="margin-top:10px; !important">
                        <p>
                            No caso de <span class="text-danger"><b>HAVER</b></span> alguma das situações acima assinaladas, por favor seleccione: Contactar os gestores forLEARN pessoalmente.
                        </p>
                            <br>
                        <p>
                            No caso de <span class="text-success"><b>NÃO HAVER</b></span> nenhuma situação acima, por favor seleccione: Tenho a certeza.
                        </p>
                   </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Contactar gestores forLEARN</button>
                    <button type="button" class="btn btn-danger" id="btn-callSubmit">Tenho a certeza</button>
                </div>
                </div>
            </div>
        </div>








































@endsection

@section('scripts')
    @parent
    <script>
     $(document).ready(function (){


         //Inicio do Cláudio JS
            //Variaveis 
            var Disciplina_id_Select=$("#Disciplina_id_Select");
            var Turma_id_Select=$("#Turma_id_Select");
            var avaliacao_id_Select=$("#avaliacao_id_Select");
            var metrica_id_Select=$("#metrica_id_Select");
            var lective_year=$("#lective_year");

            let  id_avaliacao=0;
            let  metrica_id=0;
            let  id_planoEstudo=0;
            let  discipline_id=0;
            let  whoIs="";

            //Carregar               
            ambiente();
            //Evento de mudança na select anolectivo
            lective_year.change(function(){
            //chamndo a função de mudança de frames
            ambiente();
            
            });
            //Evento de mudança na select disciplina
            Disciplina_id_Select.change(function(){
            //chamndo a função de mudança de frames
            $("#avaliacao_id_Select").empty();
            $("#students_new").empty();
            var id=Disciplina_id_Select.val();
            Turma(id,lective_year.val()); 

            
            });

            //Evento de mudança na select turma e 
            Turma_id_Select.change(function(){
            //chamndo a função de mudança de turma e trazer os estudantes
                 var lective_year=$("#lective_year").val();
                 var id=Disciplina_id_Select.val();
                 StudantGrade(discipline_id,metrica_id,id_planoEstudo,id_avaliacao,lective_year);
                    if(Turma_id_Select.val()==""){
                        avaliacao_id_Select.prop('disabled', true);   
                    }else{
                        
                        avaliacao_id_Select.prop('disabled', false);   
                    }
            });


          //Função de mudança de frame
          function ambiente(){
            var anoL=lective_year.val();
            if(anoL==6){
            $("#NextStap").hide();
            $("#tabela_new").hide();
            $("#ConteudoMain").show();
            Turma_id_Select.empty();
            }
            else{
                  //Passar o parametro de ano lectivo
                  //Neste momento colequei o ano anterior pk não há registro desse ano lectivo 
                  //Por tando no final terei de colocar a variavel (anoL)como parametro.
            Turma_id_Select.empty();   
            var anoL=lective_year.val();  
            discipline_get_new(anoL);
            // discipline_get_new();
            $("#ConteudoMain").hide();
            $("#NextStap").show();
            }   
        }
        //fim da funcção de Mundaça de frame
        //Função de pegar disciplina do professor frame
        // url: "/pt/avaliations/disciplines_teacher",
        function discipline_get_new(anolectivo){

            $.ajax({
                    url: "/pt/avaliations/disciplines_teacher/"+anolectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                  beforeSend:function(){
                      console.log("Carregando as disciplinas...")
                  }  

                 }).done(function (data){
                    // console.log(data);
                    if (data['disciplina'].length) {
                       
                       
                        $("#students_new").empty();
                        Disciplina_id_Select.prop('disabled', true);
                        Disciplina_id_Select.empty();
                        
                        $("#Disciplina_id_Select").append('<option selected="" value="00">Selecione a disciplina</option>');
                        $.each(data['disciplina'], function (index, row) {
                     
                        $("#Disciplina_id_Select").append('<option  value="'+data['whoIs']+','+ row.course_id + ','+row.discipline_id+' ">#'+row.code+'  ' + row.dt_display_name + '</option>');
                        });
                        Disciplina_id_Select.prop('disabled', false);
                        Disciplina_id_Select.selectpicker('refresh');

                     // switchRegimes(selectDiscipline[0]);
                     }else{
                        Disciplina_id_Select.empty();
                        Disciplina_id_Select.prop('disabled', true);
                        console.log("sem dados para este ano lectivo")
                     }
                 });

        }

        //fim da funcção de pegar disciplina de frame






        function Turma(id_plano,anolectivo){
          
            var re = /\s*,\s*/;
            var Planno_disciplina  = id_plano.split(re);

            $.ajax({
                    url: "/pt/avaliations/turma_teacher/"+id_plano+"/"+anolectivo,
                    type: "GET",
                    data: {
                        _token: '{{csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend:function(){
                        if(id_plano==00){ return false; }
                    },
                   
                }).done(function (data){
                    console.log(data);
                    if(data==500){
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        avaliacao_id_Select.empty();
                        avaliacao_id_Select.hide();
                        alert("Atenção! esta disciplina não está associada a nenhuma avaliação no ano lectivo selecionado, verifique a edição de plano de estudo da mesma.");
                    } 
                    else{
                        if(data['whoIs']=="super"){
                            //chama o metodo para trazer o tratamento do loop da turma   
                            TurmaLoop(data,"coordenador")
                            //para trazer outra select na avaliacao de notas
                             $("#caixaAvalicao").show();
                            //  avaliacao_id_Select.prop('disabled', true);
                             $("#avaliacao_id_Select").append('<option value="">Selecione a avaliação</option>')
                                $.each(data['avaliacao'], function (index, row) {
                                $("#avaliacao_id_Select").append('<option value="' + row.avl_id + '">' + row.avl_nome + '</option>');
                            });
                            avaliacao_id_Select.selectpicker('refresh');
                            //Termina as avaliações do coordenador
                            whoIs='';
                            whoIs=data['whoIs'];
                        }
                        else {
                        //Automático teacher.
                        whoIs='';
                        whoIs=data['whoIs'];
                         $("#caixaAvalicao").hide();  avaliacao_id_Select.empty(); 
                        //Prencher variaveis para trazer depois os alunos.
                            id_avaliacao=data['avaliacao'].avl_id;
                            metrica_id=data['metrica'][0].mtrc_id;
                            discipline_id=data['disciplina'];
                            id_planoEstudo=data['plano_estudo'];
                           
                         TurmaLoop(data,"teacher")


                        
                         }
                    }

                  

                });
              
        }





        //Selecionar_avaliacao_pega_metrica
        avaliacao_id_Select.change(function(){
            if(avaliacao_id_Select.val()!=""){  
               $("#caixaMatrica").show();
            // $("#metrica_id_Select").hide();
            var id=avaliacao_id_Select.val();
               metricasCoordenador(id)
            }
            else{
                 $("#caixaMatrica").hide();
                 $("#metrica_id_Select").empty();
            }
        });
      
        //Selecionar_avaliacao_pega_metrica
        metrica_id_Select.change(function(){
            if(metrica_id_Select.val()!=""){  
            //    $("#caixaMatrica").show();
            // $("#metrica_id_Select").hide();
            // var id=avaliacao_id_Select.val();
            studentCourse_coordenador();
              
            }
            else{

                 $("#students_new").hide();
                //  $("#metrica_id_Select").empty();
            }
        });
      


        //Metodo para trazer a turma array
        function TurmaLoop(data,titulo){
            if (data['turma'].length) {

                if (titulo=="teacher") {
                    $("#Titulo_Avalicao").empty();
                    $("#Titulo_Avalicao").text(data['avaliacao'].avl_nome +" - "+data['metrica'][0].mtrc_nome);
                    }

                    $("#tabela_new").show();
                    Turma_id_Select.prop('disabled', true);
                    Turma_id_Select.empty();

                    Turma_id_Select.append('<option selected="" value="">Selecione a turma</option>');
                        $.each(data['turma'], function (index, row) {
                    $("#Turma_id_Select").append('<option value="' + row.id + '">' + row.display_name + '</option>');
                    });

                    Turma_id_Select.prop('disabled', false);
                    Turma_id_Select.selectpicker('refresh');
                    //switchRegimes(selectDiscipline[0]);
                    }

                    else{
                    Turma_id_Select.empty();
                    Turma_id_Select.prop('disabled', true);
                    avaliacao_id_Select.prop('disabled', true);
                    
                    }

        }




        //Metrodo de trazer as metricas --de forma manual.
        function metricasCoordenador(id_avaliacao){
            $.ajax({
                    url: "/pt/avaliations/metrica_ajax_coordenador/"+id_avaliacao,
                    type: "GET",
                    data: {
                        _token: '{{csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend:function(){
                     console.log("antes das métricas...") 
                    },
                   
                }).done(function (data){
                    console.log(data);
                    if (data['metricas'].length) {
                    $("#metrica_id_Select").empty();
                    metrica_id_Select.append('<option selected="" value="">Selecione a turma</option>');
                    $.each(data['metricas'], function (index, row) {
                    $("#metrica_id_Select").append('<option value="' + row.id + '">' + row.nome + '</option>');
                    });

                    metrica_id_Select.prop('disabled', false);
                    metrica_id_Select.selectpicker('refresh');
                    }
                    else{
                        console.log(data['metricas'].length)
                        metrica_id_Select.prop('disabled', true);
                        metrica_id_Select.empty();
                    }
               
                });
 
        }
        //Fim do método
 

        //Pegar os alunos de forma manual coordenador
        function studentCourse_coordenador(){

            // var Disciplina_id_Select=$("#Disciplina_id_Select");
            // var Turma_id_Select=$("#Turma_id_Select");
            // var avaliacao_id_Select=$("#avaliacao_id_Select");
            // var metrica_id_Select=$("#metrica_id_Select");

                console.log("Id_avaliação: "+ avaliacao_id_Select.val())
                console.log("metrica_id: "+ metrica_id_Select.val() )
                console.log("id_planoEstudo: "+ id_planoEstudo)
                console.log("discipline_id: "+ Disciplina_id_Select.val())
                var turma=Turma_id_Select.val();
                console.log("turma_id: "+ turma)

            $.ajax({
                    url: "/pt/avaliations/metrica_ajax_coordenador/"+id_avaliacao,
                    type: "GET",
                    data: {
                        _token: '{{csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend:function(){
                     console.log("antes de trazer os alunos manual coordenador métricas...") 
                    },
                   
                }).done(function (data){
                    console.log(data);

                });

        }


        function StudantGrade(discipline_id,metrica_id,id_planoEstudo,id_avaliacao,lective_year){

          
                var turma=Turma_id_Select.val();

                 $.ajax({

            url: "/avaliations/student_ajax/" + discipline_id + "/" + metrica_id + "/" + id_planoEstudo + "/" + avaliacao_id + "/" + turma+ "/" + lective_year,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#students_new tr").empty();
                // console.log(dataResult);

                // var resultGrades = dataResult.data;
                var resultStudents = dataResult.students;
                // var metricArePlublished = dataResult.metricArePlublished;
                var bodyData = '';
                var i = 1;
                var flag = true;

                //Compara o utilizador e tras automático ou manual

                if(whoIs=="teacher"){

                

                if (resultStudents.length>0) {
                    var dd = 0;
                     resultStudents.forEach(function (student) {
                        bodyData += '<tr>'
                                 bodyData += "<td>"+ i++ +"</td><td width='120'><input type='checkbox'> <span id='span"+ dd+1 +"' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td> <td width='120'>"+student.n_student+"</td> <td style='font-size:0.9pc'>"+student.user_name + "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value="+student.user_id+"><input type='number' min='0' max='20' name='estudantes[]' class='form-control' value=''> ";
                        bodyData += '</tr>'
                    }); 
                }else{
                    bodyData += '<tr>'
                                bodyData += "<td class='text-center fs-2'>Nenhum estudante foi encontrado nesta turma.</td>";

                            bodyData += '</tr>'
                    }

              } else{

                console.log("coordenador");

            }

                                // for ( var a = 0; a < resultStudents.length; a++){
                                //         var dd = a;
                                //         flag = true;

                                //         //Verifica se o Array das notas está vazio
                                //         if (resultGrades == '') {
                                //             checkInputEmpty(dd);
                                //             bodyData += '<tr>'
                                //                 bodyData += "<td>"+ i++ +"</td><td width='120'><input type='checkbox' id='check"+dd+"' onclick='disbleInput("+ dd +");' checked> <input id='input"+dd+"' value='true' name='inputCheckBox[]' hidden> <span id='span"+ dd +"' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td><td width='120'>"+resultStudents[a].n_student+"</td><td>"+ resultStudents[a].user_name + "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value="+resultStudents[a].user_id+">";

                                //                 if(metrica_id == 53 || metricArePlublished) //se a metrica for igual a OA ou a metrica ja for publicada bloquear o campo (disabled)
                                //                 {
                                //                 bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' step='0.01' id="+ dd +" readOnly></td>"
                                //                 }else{
                                //                 bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' step='0.01' id="+ dd +"></td>"
                                //                 }

                                //             bodyData += '</tr>'
                                //         }else{
                                //             checkInputEmpty(dd);
                                //             bodyData += '<tr>'
                                //             bodyData += "<td>"+ i++ +"</td><td width='120'>"


                                //                 bodyData += "<input type='checkbox' id='check"+dd+"' onclick='disbleInput("+ dd +");' checked> <input id='input"+dd+"' value='true' name='inputCheckBox[]' hidden> <span id='span"+ dd +"' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td>"
                                //                 bodyData += "<td width='120'>"+resultStudents[a].n_student+"<td>"+ resultStudents[a].user_name + "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value="+resultStudents[a].user_id+">";

                                //                 if(metrica_id == 53 || metricArePlublished) //se a metrica for igual a OA bloquear o campo (disabled)
                                //                 {

                                //                     for (var b = 0; b < resultGrades.length; b++) {
                                //                         if (resultGrades[b].user_id == resultStudents[a].user_id) {
                                //                             flag = false;
                                //                             bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value="+resultGrades[b].aanota +" step='0.01' id="+ dd +" readOnly></td>"
                                //                         }
                                //                     }
                                //                     if (flag) {
                                //                         bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value='' step='0.01' id="+ dd +" readOnly></td>"
                                //                     }
                                //                 }else{

                                //                     for (var b = 0; b < resultGrades.length; b++) {
                                //                         if (resultGrades[b].user_id == resultStudents[a].user_id) {
                                //                             flag = false;
                                //                             if(avaliacao_id == 22) //caso for recurso input (max = 12)
                                //                             {
                                //                                 bodyData += "<input type='number' name='notas[]' min='0' max='12' class='form-control notas' value="+resultGrades[b].aanota +" step='0.01' id="+ dd +"></td>"
                                //                             }else{
                                //                                 bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value="+resultGrades[b].aanota +" step='0.01' id="+ dd +"></td>"
                                //                             }
                                //                         }
                                //                     }
                                //                     if (flag) {
                                //                         if (avaliacao_id == 22) //caso for recurso input (max = 12)
                                //                         {
                                //                             bodyData += "<input type='number' name='notas[]' min='0' max='12' class='form-control notas' value='' step='0.01' id="+ dd +"></td>"
                                //                         }else{
                                //                             bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value='' step='0.01' id="+ dd +"></td>"
                                //                         }
                                //                     }

                                //                 }

                                //             }


                                //             bodyData += '</tr>'
                                //         }
                                //     }
                                    /*$.each(resultGrades , function (index, row) {
                                        bodyData += '<tr>'
                                        bodyData += "<td>"+ i++ +"</td><td>"+ row.user_name + "</td><input type='hidden' name='estudantes[]' class='form-control' value="+row.user_id+"></td>";
                                        bodyData += '</tr>'
                                    })*/
                      $("#students_new").append(bodyData);

               // $('#metrica_id').prop('disabled', false);
            },
            error: function (dataResult) {
               alert('error' + result);
            }

        });


   //Ajax termina aqui

        }


        //Fim do Cláudio JS







































































          
        $("#btn-callSubmit").click(function()
            {
                $("#btn-Enviar").click();
            });
            var selectStudyPlan  = $("#course_id");
            var selectDiscipline = $("#discipline_id");
            var selectAvaliation = $("#avaliacao_id");
            var selectClass      = $("#class_id");
            var selectMetrica    = $("#metrica_id");

            getAllStudyPlanEdition();

            function getAllStudyPlanEdition(){
                $.ajax({
                    url: "/avaliations/plano_estudo_ajax/",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    //$('#container').html(data.html);

                }).done(function (data){
                    //if (dataResult.length) {
                        selectStudyPlan.prop('disabled', true);
                        selectStudyPlan.empty();


                        selectStudyPlan.append('<option selected="" value="" style="color:black;"></option>');
                        $.each(data, function (index, row) {
                            selectStudyPlan.append('<option value="' + row.spea_id + '">' + row.spea_nome + '</option>');
                        });

                        selectStudyPlan.prop('disabled', false);
                        selectStudyPlan.selectpicker('refresh');

                        //switchRegimes(selectDiscipline[0]);
                    //}
                });
            }
            //$('#discipline_id').prop('disabled', true);
            $("#class_id").prop('disabled', true);
            //Buscar Disciplinas apartir do curso associados ao Plano estudo Avaliacao
           $('#course_id').change(function(){
              var course_id = $(this).children("option:selected").val();
               console.log(course_id);
               $("#class_id").empty();
               $("#avaliacao_id").empty();
               $("#metrica_id").empty();
               $("#students tr").empty();
               $('#avaliacao_id').prop('disabled', true);
               $('#class_id').prop('disabled', true);
                $('#metrica_id').prop('disabled', true);
            //Ruanda
            if(course_id == ""){
                console.log("Empty");
                $('#discipline_id').prop('disabled', true);
                $("#discipline_id").empty();
                $('#avaliacao_id').prop('disabled', true);
                $("#class_id").prop('disabled', true);
                $("#avaliacao_id").empty();
                $('#metrica_id').prop('disabled', true);
                $('#class_id').empty("");
                $("#metrica_id").empty();
                $("#students tr").empty();
            }
            else{

            $.ajax({

            url: "/avaliations/disciplines_ajax/" + course_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'  
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#discipline_id").empty();
                console.log(dataResult);
                var resultData = dataResult.data;
                var resultClasses = dataResult.classes;
                var bodyData = '';
                var bodyClassData = '';
                var i = 1;
                console.log(dataResult.data);
                 selectDiscipline.prop('disabled', true);
                    selectDiscipline.empty();
                    selectClass.empty();

                    selectDiscipline.append('<option selected="" value=""></option>');
                    selectClass.append('<option selected="" value=""></option>');


                    $.each(resultData, function (index, row) {
                        selectDiscipline.append('<option value="' + row.discipline_id + '">' + row.dt_display_name + '</option>');
                    });

                    $.each(resultClasses, function (index, row) {
                        selectClass.append('<option value="' + row.id + '">' + row.display_name + '</option>');
                    });

                    selectDiscipline.prop('disabled', false);
                    selectDiscipline.selectpicker('refresh');

                    selectClass.prop('disabled', false);
                    selectClass.selectpicker('refresh');


            },
            error: function (dataResult) {
               // alert('error' + result);
            }
        });
   }
        });


        //Buscar Avaliações apartir do curso e disciplina associados ao Plano estudo Avaliacao
           $('#discipline_id').change(function(){
               var discipline_id = $(this).children("option:selected").val();
               console.log(discipline_id);

                $("#students tr").empty();
                $('#metrica_id').prop('disabled', true);
                $("#metrica_id").empty();
                 $("#avaliacao_id").empty();
                 $('#class_id').val("");
                 $('#class_id').prop('disabled', true);

            if(discipline_id == ""){
                console.log("Empty");
                $('#avaliacao_id').prop('disabled', true);
                $("#class_id").prop('disabled', true);
                $("#avaliacao_id").empty();
                $('#metrica_id').prop('disabled', true);
                $('#class_id').val("");
                $("#metrica_id").empty();
                $("#students tr").empty();

            }else{

            $.ajax({
            url: "/avaliations/avaliacao_ajax/" + discipline_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                 $("#avaliacao_id").empty();
                console.log(dataResult);
                var resultData = dataResult.data;
                var bodyData = '';
                var i = 1;
                console.log(dataResult.data);
                selectAvaliation.prop('disabled', true);
                selectAvaliation.empty();


                selectAvaliation.append('<option selected="" value=""></option>');
                $.each(resultData, function (index, row) {
                    selectAvaliation.append('<option value="' + row.avl_id + '">' + row.avl_nome + '</option>');
                });

                selectAvaliation.prop('disabled', false);
                selectAvaliation.selectpicker('refresh');

            },
            error: function (dataResult) {
               // alert('error' + result);
            }
        });
    }
        });
        //Buscar Metricas apartir do curso da disciplina e da avaliacao associados ao Plano estudo Avaliacao
           $('#avaliacao_id').change(function(){
               var avaliacao_id = $(this).children("option:selected").val();
               var discipline_id = $('#discipline_id').val();
               var course_id = $('#course_id').val();
                $("#students tr").empty();
                $("#metrica_id").empty();
                $('#class_id').val("");

            if(avaliacao_id == ""){
                console.log("Empty");
                $('#metrica_id').prop('disabled', true);
                $("#class_id").prop('disabled', true);
                $("#class_id").val("");
                $("#metrica_id").empty();
                $("#students tr").empty();

            }else{

            $.ajax({

            url: "/avaliations/metrica_ajax/"+avaliacao_id + "/" + discipline_id + "/" + course_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
                  },
            cache: false,
            dataType: 'json',
            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#metrica_id").empty();
                console.log(" Teste básico "+dataResult);
                var resultData = dataResult.data;
                var bodyData = '';
                var i = 1;
                console.log(dataResult.data);
                selectMetrica.prop('disabled', true);
                selectMetrica.empty();

                selectMetrica.append('<option selected="" value=""></option>');

                $.each(resultData, function (index, row) {
                    selectMetrica.append('<option value="' + row.mtrc_id + '">' + row.mtrc_nome + '</option>');
                });

                selectMetrica.prop('disabled', false);
                selectMetrica.selectpicker('refresh');
                //$("#class_id").prop('disabled', false)//
                
                selectClass.prop('disabled', false);
            },
            error: function (dataResult) {
               // alert('error' + result);
            }
        });
    }
});




    $("#class_id").change(function(){
        if($("#class_id").val() == "")
        {
            $("#metrica_id").val("");
            $("#students tr").empty();
            $('#metrica_id').prop('disabled', true);
        }else{
             var avaliacao_id = $("#avaliacao_id").val();
             var discipline_id = $('#discipline_id').val();
             var course_id = $('#course_id').val();

                $.ajax({

                url: "/avaliations/metrica_ajax/"+ avaliacao_id + "/" + discipline_id + "/" + course_id,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function (dataResult) {
                    console.log(dataResult);
                    //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                    var resultData = dataResult.data;
                    var bodyData = '';
                    var i = 1;
                    selectMetrica.prop('disabled', true);
                    selectMetrica.empty();

                    selectMetrica.append('<option selected="" value=""></option>');
                    $.each(resultData, function (index, row) {
                        selectMetrica.append('<option value="' + row.mtrc_id + '">' + row.mtrc_nome + '</option>');
                    });


                    selectMetrica.prop('disabled', false);
                    selectMetrica.selectpicker('refresh');


                     //$("#class_id").prop('disabled', false);
                    selectClass.prop('disabled', false);
                },
                error: function (dataResult) {
                    // alert('error' + result);
                }
            });

            $("#students tr").empty();
        }


    });

    //Listar estudante que tem a determinada disciplina e determinada turma.
        $("#metrica_id").change(function(){

            if($("#metrica_id").val() == "")
            {
                $("#students tr").empty();

            } else {
            var discipline_id = $('#discipline_id').val();
            var metrica_id = $('#metrica_id').val();
            var course_id = $('#course_id').val();
            var avaliacao_id = $('#avaliacao_id').val();
            var class_id = $('#class_id').val();

            $.ajax({

            url: "/avaliations/student_ajax/" + discipline_id + "/" + metrica_id + "/" + course_id + "/" + avaliacao_id + "/" + class_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#students tr").empty();
                console.log(dataResult);

                var resultGrades = dataResult.data;
                var resultStudents = dataResult.students;
                var metricArePlublished = dataResult.metricArePlublished;
                var bodyData = '';
                var i = 1;
                var flag = true;

                var a; for (a = 0; a < resultStudents.length; a++)
                 {
                     var dd = a;
                     flag = true;

                    //Verifica se o Array das notas está vazio
                    if (resultGrades == '') {
                        checkInputEmpty(dd);
                        bodyData += '<tr>'
                        bodyData += "<td>"+ i++ +"</td><td width='120'><input type='checkbox' id='check"+dd+"' onclick='disbleInput("+ dd +");' checked> <input id='input"+dd+"' value='true' name='inputCheckBox[]' hidden> <span id='span"+ dd +"' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td><td width='120'>"+resultStudents[a].n_student+"</td><td>"+ resultStudents[a].user_name + "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value="+resultStudents[a].user_id+">";

                        if(metrica_id == 53 || metricArePlublished) //se a metrica for igual a OA ou a metrica ja for publicada bloquear o campo (disabled)
                        {
                          bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' step='0.01' id="+ dd +" readOnly></td>"
                        }else{
                           bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' step='0.01' id="+ dd +"></td>"
                        }

                        bodyData += '</tr>'
                    }else{
                        checkInputEmpty(dd);
                        bodyData += '<tr>'
                        bodyData += "<td>"+ i++ +"</td><td width='120'>"


                            bodyData += "<input type='checkbox' id='check"+dd+"' onclick='disbleInput("+ dd +");' checked> <input id='input"+dd+"' value='true' name='inputCheckBox[]' hidden> <span id='span"+ dd +"' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td>"
                            bodyData += "<td width='120'>"+resultStudents[a].n_student+"<td>"+ resultStudents[a].user_name + "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value="+resultStudents[a].user_id+">";

                            if(metrica_id == 53 || metricArePlublished) //se a metrica for igual a OA bloquear o campo (disabled)
                            {

                                for (var b = 0; b < resultGrades.length; b++) {
                                    if (resultGrades[b].user_id == resultStudents[a].user_id) {
                                        flag = false;
                                        bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value="+resultGrades[b].aanota +" step='0.01' id="+ dd +" readOnly></td>"
                                    }
                                }
                                if (flag) {
                                    bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value='' step='0.01' id="+ dd +" readOnly></td>"
                                }
                             }else{

                                for (var b = 0; b < resultGrades.length; b++) {
                                    if (resultGrades[b].user_id == resultStudents[a].user_id) {
                                        flag = false;
                                        if(avaliacao_id == 22) //caso for recurso input (max = 12)
                                        {
                                            bodyData += "<input type='number' name='notas[]' min='0' max='12' class='form-control notas' value="+resultGrades[b].aanota +" step='0.01' id="+ dd +"></td>"
                                        }else{
                                            bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value="+resultGrades[b].aanota +" step='0.01' id="+ dd +"></td>"
                                        }
                                    }
                                }
                                if (flag) {
                                    if (avaliacao_id == 22) //caso for recurso input (max = 12)
                                    {
                                        bodyData += "<input type='number' name='notas[]' min='0' max='12' class='form-control notas' value='' step='0.01' id="+ dd +"></td>"
                                    }else{
                                        bodyData += "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value='' step='0.01' id="+ dd +"></td>"
                                    }
                                }



                            }

                        // }


                        bodyData += '</tr>'
                     }
                }
                    /*$.each(resultGrades , function (index, row) {
                        bodyData += '<tr>'
                        bodyData += "<td>"+ i++ +"</td><td>"+ row.user_name + "</td><input type='hidden' name='estudantes[]' class='form-control' value="+row.user_id+"></td>";
                        bodyData += '</tr>'
                    })*/
                $("#students").append(bodyData);

               // $('#metrica_id').prop('disabled', false);
            },
            error: function (dataResult) {
               // alert('error' + result);
            }

        });



        }


        });



        });

        function disbleInput(dd)
        {
            var checkStatus = document.getElementById("check"+dd+"").checked;
            var inputGrade = document.getElementById(dd);
            var span = document.getElementById("span"+dd+"");

            if (checkStatus == true) {
                inputGrade.readOnly = false;
                document.getElementById("input"+dd+"").value = "";
                document.getElementById("input"+dd+"").value = true;
                // inputGrade.value = "true";
                //document.getElementById("check"+dd+"").disabled = true;
                span.style.backgroundColor = "#38C172";
                span.innerHTML = "PRESENTE";
            }else{
                 inputGrade.readOnly = true
                 document.getElementById("input"+dd+"").value = "";
                 document.getElementById("input"+dd+"").value = false;
                // checkStatus.prop( "checked" );
                //  inputGrade.value = "false";
                //document.getElementById("check"+dd+"").disabled = true;
                span.style.backgroundColor = "red";
                span.innerHTML = "AUSENTE";
            }

        }

        function checkInputEmpty(dd) {
            var inputGrade = document.getElementById(dd);
                //var dpe = document.getElementById(dd).value;
                //console.log(inputGrade);
        }
    </script>

@endsection

   