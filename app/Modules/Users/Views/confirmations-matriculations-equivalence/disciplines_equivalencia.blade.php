

@php
$years = []; 
(int)$countDiscipline = 0; 
$especialidade = []; 
$basta = 0; 
$condi = 0;  
$countReprovad = count($disciplinesReproved);   
@endphp

@if (isset($estado['error']) && $estado['error'] == "yess")
<h3>Atenção!</h3><br>
<div class="alert-warning p-3">
    <p>A <b>forLEARN</b> detectou que este é um estudante vindo por equivalência. No entanto existe alguns parâmetros que possivelmente não foram cumpridos ao rigor e está impedindo a disponibilização das disciplinas para efetuar a matrícula do mesmo. Por favor verifica se cumpriu com os passos todos listados abaixo:</p>
    <br>
    <label>1º Efetuou devidamente o lançamento das notas positivas das disciplinas que equivalem?</label>
    <label>2º As notas lançadas são todas elas positivas?</label>
    <label>3º Se adicionou uma nova disciplina após lançar as notas, fez a atualização das notas para que a nota desta reflita?</label>
    <label>4º Houve alguma mudança de curso após o lançamento das notas?</label>
    <br><br>
    <p>Caso todas as etapas acima descritas forem validadas devidamente e o problema persistir, contactar o apoio a <b>forLEARN</b>.</p>
</div>
@elseif(!empty($curricularPlanDisciplines))
    @isset($info)
    @if ($info != "")
    @php
        $condi = 1;
    @endphp
        <div class="alert alert-warning" role="alert">
            {{ $countReprovad != 0 ? 
                "Atenção: Para prosseguir com esta matrícula com a condição de mudança de curso automática, primeiro deve-se efetuar o lançamento de nota(s) positiva(s) da(s) cadeira(s) em atraso para que a mudança ocorra sem antecedentes negativos." : 
                $info 
            }}
        </div>
        <input type="hidden" value="change" name="course_change">
    @endif 
    @endisset

    <div id="ContainerSeguinte" data-count="{{count($curricularPlanDisciplines)}}">

        @foreach ($curricularPlanDisciplines as $year => $curricularPlanDisciplines)
        <div class="only_grade_{{$year}}" >
             <input type="hidden" name="years[]" value="{{ $year }}">

            <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
            @php array_push($years, $year); @endphp
            <span style="font-weight: bold; margin-right: 6px">Turma:</span>
            <select name="classes[{{ $year }}]" id="">
                @foreach ($classes as $class)
                    @foreach ($curricularPlanDisciplines as $validarCursoA) @endforeach
                    @if ($class->year == $year && $class->courses_id == $validarCursoA['courses_id'])
                        <option value="{{ $class->id }}">{{ $class->display_name }}</option>
                    @endif
                @endforeach
            </select>       
            <ul style="list-style: none" class="Lista_disciplina">
                @foreach ($curricularPlanDisciplines as $discipline)
                    @php
                        $especialidade[] = [
                            "codigo" => $discipline['code'],
                            "id_disciplina" => $discipline['discipline_id']
                        ];
                    @endphp
                    <li class="item_list_disciplines_{{ $discipline['discipline_id'] }}">
                        <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id'] }}" 
                            name="disciplines[{{ $discipline->years }}][]" 
                            value="{{ $discipline['discipline_id'] }}" 
                            data-id="{{ $discipline['discipline_id'] }}" 
                            class="check-discipline form-check-input-center" 
                            required checked>
                        <label for="{{ $discipline['discipline_id'] }}">
                            #{{ $discipline['code'] }} - {{ $discipline['display_name'] }}
                        </label>
                    </li>
                @endforeach
            </ul>

        </div>

        @endforeach
    </div>

    <div hidden>
        <ul>
            <li>@php count($years); @endphp</li>
        </ul>
    </div>
@else
    <h5 class="card-title mb-2">Sem disciplinas</h5>
@endif

<script>
    var CEE = "{{ $basta }}";
    if (CEE == 1) {
        console.log("Ocultou da forma tradicional___");
        $("#ContainerSeguinte").remove();
    }

    var Mudanca = "{{ $countReprovad }}";
    var condi = "{{ $condi }}";
    if (condi == 1) {
        $("#groupBTNconf").attr("hidden", true);
    } else {
        $("#groupBTNconf").attr("hidden", false);
    }

    var flag = false;
    var Removido = [];
    var dados = $(".Lista_disciplina").html();
    $("#espcialidadeCEE").change(function() {
        console.clear();
        var valor_select = $("#espcialidadeCEE").val();
        var codigoespecialidade = @json($especialidade);
        if (flag) {
            $(".Lista_disciplina").html(dados);
            toogle(codigoespecialidade, valor_select);
        } else {
            toogle(codigoespecialidade, valor_select);
        }
    });

    function toogle(codigoespecialidade, valor_select) {
        codigoespecialidade.forEach(element => {
            var code = element['codigo'].slice(0, 3);
            var id = element['id_disciplina'];
            if (code != valor_select) {
                flag = true;
                $("li").remove(".item_list_disciplines_" + id);
            }
        });
    }
</script>
