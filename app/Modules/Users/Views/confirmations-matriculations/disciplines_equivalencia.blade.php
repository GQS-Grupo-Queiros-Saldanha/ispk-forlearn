@php $years = []; (int)$countDiscipline = 0; $especialidade=[]; $basta=0; $condi=0;  $countReprovad=count($disciplinesReproved);   @endphp



@if (isset($estado['error']) && $estado['error']=="yes")
    <h3>Atenção!</h3><br>
    <div class="alert-warning p-3">
        <p>A <b>forLEARN</b> detectou que este é um estudante vindo por equivalência. No entanto existe alguns paramêtros que possivelmente não foram compridos ao rigor
            e está impedindo a disponibilização das disciplinas para efectuar a matrícula do mesmo. Por favor verifica se compriu com os passos todos listados abaixo: 
        </p>
        <br>
        <label for="">1º Efectuou o devidamente o lançamento das notas positivas das disciplinas que equivalem ?</label>
        <label for="">2º As notas lançadas são todas elas positivas?</label>
        <label for="">3º Se adicionou uma nova disciplina após lançar as notas, fez a actualização das notas para que a nota desta reflita?</label>
        <label for="">4º Houve alguma mudança de curso após o lançamento das notas?</label>

        <br>
        <br>
        <p>Caso todas etapas acima descritas forem validadas devidamente e o problema persistir, contactar o apoio a <b>forLEARN</b>.
        </p>

    </div>

@elseif(!empty($disciplinesReproved) && !empty($curricularPlanDisciplines))

    {{-- @php $years = []; (int)$countDiscipline = 0; $especialidade=[]; $basta=0; $condi=0;  $countReprovad=count($disciplinesReproved);   @endphp --}}
    @isset($info)
        @if ($info!="")
            @php
                $condi=1;
            @endphp
                <div class="alert alert-warning" role="alert">
                    {{$countReprovad!=0? "Atenção: Para prosseguir com esta matrícula com a condição de mudança de curso automática,
                    primeiro deve-se efectuar o lançamento de nota(s) positiva(s) da(s) cadeira(s) em atraso para que a mudança ocorra sem antecedentes negativos.": $info}}
                </div>
                <input type="hidden" value="change" name="course_change">
        @endif 
    @endisset 

@foreach ($disciplinesReproved as $year => $disciplinesReproved)
    
        <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
        @php array_push($years, $year); @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
            <select name="classes[{{$year}}]" id="">
   
                @foreach ($classes as $class)
                    @foreach ($disciplinesReproved as $validarCurso)
                  
                    @endforeach
                        @if ($class->year == $year &&  $class->courses_id==$validarCurso['courses_id'])
                            <option value="{{$class->id}}">
                                {{ $class->display_name }}
                            </option>
                        @endif
                @endforeach
            </select>

         <ul style="list-style:none">
            @foreach ($disciplinesReproved as $discipline)
                  <li>
                    @php $countDiscipline++; @endphp
                    <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id']}}" onclick="showSelect({{$discipline['discipline_id']}})" value="{{$discipline['discipline_id']}}" name="disciplines[{{$discipline->years}}][]" value="{{ $discipline['discipline_id'] }}" class="check-discipline form-check-input-center" data-id="{{$discipline['discipline_id']}}" required>
                        <label for="{{ $discipline['discipline_id']}}">
                            #{{$discipline['code']}}-{{$discipline['display_name']}}
                        </label>
                        <br>
                    <div class="checkbox" id="checkbox_group_{{$discipline['discipline_id']}}" hidden>
                            <label for="" class="form-check-label" style="color:#595959; margin-left: 20px">
                                 <input type="checkbox" id="checkbox_item_{{$discipline['discipline_id']}}" value="{{ $discipline['discipline_id'] }}" class="check-discipline-regime form-check-input-center" data-id="checkbox_item_{{$discipline['discipline_id']}}" name="disciplines_exam_only[{{$discipline->years}}][]" disabled>
                                 <span>Inscrição para exame</span>
                             </label>
                    </div>
                   </li>
            @endforeach
        </ul>
    @endforeach
    
    

    @if(isset($estado['curso'])=="CEE" && $nextYear==3 &&  $countDiscipline>0)
        @php
            $basta=1;
        @endphp
    @endif

    <div id="ContainerSeguinte">
     @if (isset($estado['pontos']) < 5 && isset($estado['curso'])!="CEE" && $nextYear==3  || $estado['Obs']=="normal" &&  isset($estado['curso'])!="CEE" ||$estado['Obs']=="normal" && isset($estado['curso'])!="CEE" && $estado['pontos'] == 0 && $nextYear==4 ||  $estado['pontos'] >= 5 && $estado['estado']=="aprovado" && isset($estado['curso'])=="RI" || isset($estado['curso'])=="CEE" && $nextYear==3 &&  $countDiscipline<1 )
  
    @foreach ($curricularPlanDisciplines as $year => $curricularPlanDisciplines)
    @if ($year <= $nextYear) 
       {{-- Ano curricular: {{$year}}º - {{$nextYear }}º Ano - Estado: {{$estado['pontos'] }}  curso:{{$estado['curso']}} --}}
        <h5 class="card-title mb-2">{{$year}}º Ano</h5>
        @php array_push($years, $year); @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
        <select name="classes[{{$year}}]" id="">
            @foreach ($classes as $class)
                @foreach ($curricularPlanDisciplines as $validarCursoA)
                
                @endforeach
                    @if ($class->year == $year &&  $class->courses_id==$validarCursoA['courses_id'])
                        <option value="{{$class->id}}">
                        {{ $class->display_name }}
                        </option>
                    @endif
            @endforeach
        </select>       


    @if(isset($estado['curso'])==="CEE" && $year===3 && $countDiscipline<1 || isset($estado['curso'])==="CEE" && $year===4)
        <br>
        <span class="font-weight: bold; margin-right: 6px">Especialidade:</span>
            <select name="especialidade_{{$estado['curso']}}" id="espcialidadeCEE">
                <option value="">Seleciona a especialidade</option>
                <option value="COA">COA</option>
                <option value="GEE">GEE</option>
                <option value="ECO">ECO</option>          
        </select>
    @endif

    <ul style="list-style: none" class="Lista_disciplina">
            @foreach ($curricularPlanDisciplines as $discipline)
                   
                @php
                   $especialidade[]=[
                    "codigo"=>$discipline['code'],
                    "id_disciplina"=>$discipline['discipline_id']
                ];

                @endphp
                <li class="item_list_disciplines_{{ $discipline['discipline_id']}}">
                  <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id']}}" name="disciplines[{{$discipline->years}}][]" value="{{ $discipline['discipline_id'] }}" data-id="{{ $discipline['discipline_id'] }}" class="check-discipline form-check-input-center" required checked>
                  <label for="{{ $discipline['discipline_id']}}">
                     #{{ $discipline['code']}} - {{$discipline['display_name'] }}
                  </label>
                </li>


            @endforeach
            </ul>
            @endif
            @break;
            @endforeach
            @endif
  
        </div>


            <div hidden>
                <ul>
                    <li>
                       @php
                           
                         count($years); 
                       @endphp
                       @if(isset($estado['curso'])=="CEE" && $nextYear==3 &&  $countDiscipline>0)
                         @foreach ($years as $year)
                         <input type="text" name="years[]" value="{{ $year }}">
                            @if (count($years)==1 && count($years)<3 || count($years)==2 && count($years)<3)
                                @break;
                            @endif
                         @endforeach
                        @else

                        @foreach ($years as $year)
                             <input type="text" name="years[]" value="{{ $year }}">
                        @endforeach
                        @endif
                    </li>
                </ul>
            </div>

@else
    <h5 class="card-title mb-2">Sem disciplinas :( </h5>
@endif


<script>
   //Para ocultar a div quando dor 3 ANO cee com cadeira
   var CEE= "{{$basta}}";
    if (CEE==1) {
        console.log("Ocultou da forma tradicional___")
        $("#ContainerSeguinte").remove();
    }
   //Fim do caso CEE

   //Para ocultar o botão de confrmar se mudança ter matrícula
   var Mudanca= "{{$countReprovad}}";
   var condi= "{{$condi}}";
   $("#groupBTNconf").removeClass("d-none");

    
   //Fim do 

   var flag=false;
   var Removido=[];
   var dados= $(".Lista_disciplina").html();
    $("#espcialidadeCEE").change(function(){
        console.clear();
         //Pegar o valor actual  do selector
        var valor_select= $("#espcialidadeCEE").val();
        var codigoespecialidade = @json($especialidade);

        if (flag) {
            $(".Lista_disciplina").html(dados); 
             toogle(codigoespecialidade,valor_select);
         }else{
            //Loop no código de cada disciplina para sabe a especialidade a selecionar
            toogle(codigoespecialidade,valor_select);
         }
    });



    //funcão para toogle_disciplina do curso CEE do 3º ano
    function toogle(codigoespecialidade,valor_select){
        codigoespecialidade.forEach(element => {
        var code=element['codigo'].slice(0,3);
        var id=element['id_disciplina'];

            if (code!=valor_select) {
                flag=true;
                //Remove os elementos da tela
                $("li").remove(".item_list_disciplines_"+id);
            }
        });   
    }

</script>
















