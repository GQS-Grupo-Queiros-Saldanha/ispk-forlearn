@section('title',__('Visualizar Plano de Estudos Avaliação'))
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
                            Exibir Pauta Final
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

                                {{-- <div class="row" >
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecione o ano do curso</label>
                                            <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="anoCurso_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina" tabindex="-98">
                   
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}

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

                                 


                                {{-- <div class="row" id="caixaAvalicao">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecione a métrica</label>
                                            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="selector_metricas" data-actions-box="false" data-selected-text-format="values" name="selector_metricas" tabindex="-98">
                                                           
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}
                                
                                {{-- <div class="row " id="metrica_Oa" style="visibility: hidden;">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecionar AO</label>
                                            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="selector_ao" data-actions-box="false" data-selected-text-format="values" name="selector_ao" tabindex="-98">
                                                                                                           
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}

                            </div>
                            <input type="hidden" value="0" id="verificarSelector">
                            <hr>
                            <div class="card">

                                <div class="float-right">
                                    <div class="row">
                                        <div class="col-8">
                                            <a class="btn btn-primary" id="generate-pdf" target="_blank">
                                                IMPRIMIR test
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="card mr-1">
                                    <h4 id="titulo_semestre"></h4>
                                    <table class="table">
                                        <thead class="listaMenu" >
                                            <tr id="listaMenu">
                                               
                                            </tr>
                                        </thead>
                                        <tbody id="lista_tr">
                                          
                                        </tbody>
                                      </table>
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
                var  anoCurso_id_Select=$("#anoCurso_id_Select")
                var curso=$("#curso_id_Select")
                var Disciplina_Select=$("#Disciplina_id_Select")
                var Turma_id_Select=$("#Turma_id_Select")


                // Criar variaves array para organizar o Menu.
                var vetorCalendario=[]
                var vetorCalendarioVasio=[]

                var vetorCalendarioMetrica=[]
                var vetorCalendarioVasioMetrica=[]
                getCurso(id_anoLectivo);

                id_anoLectivo.bind('change keypress',function(){
                    console.log("123");
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
                         
                        if(data==500){
                            Turma_id_Select.empty();
                            Turma_id_Select.prop('disabled', true);
                            alert("Atenção! este curso não está associada a nenhuma turma no ano lectivo selecionado, verifique a edição de plano de estudo do mesma.");
                        } 
                        if (data['data'].length>0) {
                            Turma_id_Select.empty();     
                            Turma_id_Select.append('<option selected="" value="0">Selecione a disciplina</option>');

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
                        // console.log(data);
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

                    // console.log("T",Turma_id_Select.val(),"A",id_anoLectivo.val(),"C",id_curso[0],"D",id_disciplinaVetor[0],"AC",anoCurso_id_Select.val(),"D1",id_disciplinaVetor[1], id_disciplinaVetor)

                    $.ajax({
                        url: "/avaliations/getMenuAvaliacoesDisciplina/"+Turma_id_Select.val()+"/"+id_anoLectivo.val()+"/"+id_curso[0]+"/"+id_disciplinaVetor[0]+"/"+anoCurso_id_Select.val()+"/"+id_disciplinaVetor[1],
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                    }).done(function (data){
                        //  console.log(data)
                         var tabelatr=""
                         var vetorCalendario=[]
                         var vetorCalendarioVasio=[]
        
                         var vetorCalendarioMetrica=[]
                         var vetorCalendarioVasioMetrica=[]

                        if(data==500){
                            Turma_id_Select.empty();
                            Turma_id_Select.prop('disabled', true);
                            alert("Atenção! este curso não está associada a nenhuma Prova no ano lectivo selecionado.");
                        } 

                         if(data['data'].length>0){
                            $("#listaMenu").empty(); 
                            tabelatr="<td> #</td><td> Nome</td>"
                            // tabelatr=""
                            $.each(data['data'], function (indexInArray, row) { 
                                if (row.date_endProva==null) {
                                    vetorCalendarioVasio.push(row.nome_avaliacao)
                                }else{
                                    vetorCalendario.push(row.nome_avaliacao)
                                }      
                            });
                            $.each(vetorCalendarioVasio, function (indexInArray, row) {     
                                vetorCalendario.push(row)
                            });
                            
                            // Estrura de repetição que pega as metricas.
                            $.each(data['vetor'], function (indexInArray, row) { 
                                if (row.date_incioMetrica==null) {
                                    vetorCalendarioVasioMetrica.push(row.nome_mterica)
                                }else{
                                    vetorCalendarioMetrica.push(row.nome_mterica)
                                }      
                            });
                            vetorCalendarioVasioMetrica.sort()
                            $.each(vetorCalendarioVasioMetrica, function (indexInArray, row) {     
                                vetorCalendarioMetrica.push(row)
                            });
                         
                            
                            // Estrura que cria o menu
                            $.each(vetorCalendario, function (indexInArray, row) { 
                                $.each(data['vetor'], function (index, linha) { 
                                    $.each(vetorCalendarioMetrica, function (InArray, rowsMetrica) {  
                                        if (row==linha.nome_avaliacao &&  rowsMetrica==linha.nome_mterica &&  linha.nome_mterica!=null && rowsMetrica!=null) {
                                            tabelatr+="<td>"+rowsMetrica+"</td>"
                                            console.log(rowsMetrica);
                                        }
                                    });
                                      
                                });
                                 tabelatr+="<td>"+row+"</td>"
                            });
                            
                         } 
                         tabelatr+="<td>Observações</td>"
                         $("#listaMenu").append(tabelatr);
                         getStudentNotasPautaFinal(vetorCalendario,vetorCalendarioMetrica)                                
                    });
                })
               
                function getStudentNotasPautaFinal(vetorCalendario,vetorCalendarioMetrica) {  
                    var vetorCurso= curso.val();
                    var re = /\s*,\s*/;
                    var id_curso  = vetorCurso.split(re);

                    var  vetorDisciplina= Disciplina_Select.val();
                    var vetor = /\s*,\s*/;
                    var id_disciplinaVetor  = vetorDisciplina.split(vetor);

                    $.ajax({
                        url: "/avaliations/getStudentNotasPautaFinal/"+id_anoLectivo.val()+"/"+id_curso[0]+"/"+Turma_id_Select.val()+"/"+id_disciplinaVetor[0],
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                    }).done(function (data){
                            // console.log(data); 
                            var i=1;
                            var tabelatr="";
                            var resultados_student=data['data']['dados']
                            // console.log(vetorCalendario);
                            // console.log(vetorCalendarioMetrica);
                            if(data==500){
                                alert("Atenção! este curso não está associada a nenhuma turma no ano lectivo selecionado, verifique a edição de plano de estudo do mesma.");
                            }else{
                                $("#lista_tr").empty();
                                $.each(resultados_student, function (index, item) { 
                                    tabelatr+="<tr><td>"+i+
                                    
                                    "</td></tr>"
                                });
                                $("#lista_tr").append(tabelatr);
                            }                               
                    });
                }


      
          })
     
    </script>
@endsection

{{-- logica que permite saber quantos valor estão repetido no Array --}}
{{-- var current = null;
var cnt = 0; --}}
{{-- for (var i = 0; i < vetorCalendario.length; i++) {
 
    if (vetorCalendario[i] != current) {
        if (cnt > 0) {
            
            console.log(current+" - aqui" + cnt );
        }
        current = vetorCalendario[i];
        cnt = 1;
    } else {
        cnt++;
    }
} --}}
