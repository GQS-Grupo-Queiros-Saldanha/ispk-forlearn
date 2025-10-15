
@if (!empty($disciplinesReproved) && !empty($curricularPlanDisciplines))
    @php
        $years = [];
        (int) ($countDiscipline = 0);
        $especialidade = [];
        $basta = 0;
        $condi = 0;
        $countReprovad = count($disciplinesReproved);
    @endphp
    @isset($info)
        @if ($info != '')
            @php
                $condi = 1;
            @endphp
            <div class="alert alert-warning" role="alert">
                {{ $countReprovad != 0
                    ? "Atenção: Para prosseguir com esta matrícula com a condição de mudança de curso automática,
                             primeiro deve-se efectuar o lançamento de nota(s) positiva(s) da(s) cadeira(s) em atraso para que a mudança ocorra sem antecedentes negativos."
                    : $info }}
            </div>
            <input type="hidden" value="change" name="course_change">
        @endif
    @endisset

    @foreach ($disciplinesReproved as $year => $disciplinesReproved)
        <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
        @php array_push($years, $year); @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
        <select name="classes[{{ $year }}]" id="classes[{{ $year }}]" class="select_turm"
            key="{{ $year }}">
            <option value="">Seleciona a turma</option>
            @foreach ($classes as $class)
                @foreach ($disciplinesReproved as $validarCurso)
                @endforeach
                @if ($class->year == $year && $class->courses_id == $validarCurso['courses_id'])
                    <option value="{{ $class->id }}">
                        {{ $class->display_name }}
                    </option>
                @endif
            @endforeach
        </select>

        <ul style="list-style: none">
            @foreach ($disciplinesReproved as $discipline)
                <li class="item_classes_{{ $year }}">
                    @php $countDiscipline++; @endphp
                    <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id'] }}"
                        onclick="showSelect({{ $discipline['discipline_id'] }})"
                        value="{{ $discipline['discipline_id'] }}" name="disciplines[{{ $discipline->years }}][]"
                        value="{{ $discipline['discipline_id'] }}" class="check-discipline form-check-input-center"
                        data-id="{{ $discipline['discipline_id'] }}" required>
                    <label for="{{ $discipline['discipline_id'] }}">
                        #{{ $discipline['code'] }}-{{ $discipline['display_name'] }}
                    </label>
                    <br>
                    <div class="checkbox" id="checkbox_group_{{ $discipline['discipline_id'] }}" hidden>
                        <label for="" class="form-check-label" style="color:#595959; margin-left: 20px">
                            <input type="checkbox" id="checkbox_item_{{ $discipline['discipline_id'] }}"
                                value="{{ $discipline['discipline_id'] }}"
                                class="check-discipline-regime form-check-input-center"
                                data-id="checkbox_item_{{ $discipline['discipline_id'] }}"
                                name="disciplines_exam_only[{{ $discipline->years }}][]" disabled>
                            <span>Inscrição para exame</span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    @endforeach

    @if ($estado['curso'] == 'CEE' && $nextYear == 3 && $countDiscipline > 0)
        @php
            $basta = 0;
        @endphp
    @endif

    <div id="ContainerSeguinte" style="margin-top: -20px;">
        @if  (
            ($estado['pontos'] < 5 && $estado['curso'] == 'CEE' && $nextYear == 3) ||
                ($estado['Obs'] == 'normal' && $estado['curso'] != 'CEE') ||
                ($estado['Obs'] == 'normal' && $estado['curso'] != 'CEE' && $estado['pontos'] == 0 && $nextYear == 4) ||
                ($estado['pontos'] >= 5 && $estado['estado'] == 'aprovado' && $estado['curso'] == 'RI') ||
                ($estado['curso'] == 'CEE' && $nextYear == 3 && $countDiscipline < 1) ||
                ($estado['pontos'] < 5 && $estado['curso'] == 'CEE' && $nextYear > 3))
        

            @foreach ($curricularPlanDisciplines as $year => $curricularPlanDisciplines)
      
                @if ($year <= $nextYear)
                   
                    <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
                    @php array_push($years, $year); @endphp
                    <span class="font-weight: bold; margin-right: 6px">Turma:</span>
                    <select name="classes[{{ $year }}]" id="classes[{{ $year }}]" class="select_turm"
                        key="{{ $year }}">
                        <option value="">Seleciona a turma</option>
                        @foreach ($classes as $class)
                            @foreach ($curricularPlanDisciplines as $validarCursoA)
                            @endforeach
                            @if ($class->year == $year && $class->courses_id == $validarCursoA['courses_id'])
                                <option value="{{ $class->id }}">
                                    {{ $class->display_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>


                    @if (($estado['curso'] === 'CEE' && $year === 3 && $countDiscipline < 1) || ($estado['curso'] === 'CEE' && $year === 4))
                        <br>
                        <span class="font-weight: bold; margin-right: 6px">Especialidade:</span>
                        <select name="especialidade_{{ $estado['curso'] }}" id="espcialidadeCEE">
                            <option value="">Seleciona a especialidade</option>
                            <option value="COA">COA</option>
                            <option value="GEE">GEE</option>
                            <option value="ECO">ECO</option>
                        </select>
                    @endif


                    <ul style="list-style: none" class="Lista_disciplina">
                        @foreach ($curricularPlanDisciplines as $discipline)
                            @php
                                $especialidade[] = [
                                    'codigo' => $discipline['code'],
                                    'id_disciplina' => $discipline['discipline_id'],
                                ];
                            @endphp
                            <li
                                class="item_list_disciplines_{{ $discipline['discipline_id'] }} item_classes_{{ $year }}">
                                <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id'] }}"
                                    name="disciplines[{{ $discipline->years }}][]"
                                    value="{{ $discipline['discipline_id'] }}"
                                    data-id="{{ $discipline['discipline_id'] }}"
                                    class="check-discipline form-check-input-center" required checked>
                                <label for="{{ $discipline['discipline_id'] }}">
                                    #{{ $discipline['code'] }} - {{ $discipline['display_name'] }}
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
            @if ($estado['curso'] == 'CEE' && $nextYear == 3 && $countDiscipline > 0)
                @foreach ($years as $year)
                    <input type="text" name="years[]" value="{{ $year }}">
                    @if ((count($years) == 1 && count($years) < 3) || (count($years) == 2 && count($years) < 3))
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
<h5 class="card-title mb-2">Sem disciplinas</h5>


@endif





<script>
    //Para ocultar a div quando dor 3 ANO cee com cadeira
    var CEE = "{{ $basta }}";

    var selectTurm = $('.select_turm');

    if (CEE == 1) {
        console.log("Ocultou da forma tradicional___")
        $("#ContainerSeguinte").remove();
    }
    //Fim do caso CEE

    //Para ocultar o botão de confrmar se mudança ter matrícula
    var Mudanca = "{{ $countReprovad }}";
    var condi = "{{ $condi }}";
    if (Mudanca != 0 && condi == 1) {
        $("#groupBTNconf").attr("hidden", true);
    } else {
        $("#groupBTNconf").attr("hidden", false);
    }
    //Fim do 

    var flag = false;
    var Removido = [];
    var dados = $(".Lista_disciplina").html();
    $("#espcialidadeCEE").change(function() {
        console.clear();
        //Pegar o valor actual  do selector
        var valor_select = $("#espcialidadeCEE").val();
        var codigoespecialidade = @json($especialidade);

        if (flag) {
            $(".Lista_disciplina").html(dados);
            toogle(codigoespecialidade, valor_select);
        } else {
            //Loop no código de cada disciplina para sabe a especialidade a selecionar
            toogle(codigoespecialidade, valor_select);
        }
    });



    //funcão para toogle_disciplina do curso CEE do 3º ano
    function toogle(codigoespecialidade, valor_select) {
        codigoespecialidade.forEach(element => {
            var code = element['codigo'].slice(0, 3);
            var id = element['id_disciplina'];

            if (code != valor_select) {
                flag = true;
                //Remove os elementos da tela
                $("li").remove(".item_list_disciplines_" + id);
            }
        });
    }


    function openClosedLi(obj) {
        let clsItem = ".item_classes_" + obj.attr('key');
        let groupBTNconf = $("#groupBTNconf");
        let items = $(clsItem);

        if (obj.val() == "") {

            items.each((i, m) => {
                if (!m.classList.contains('d-none')) {
                    m.classList.add('d-none');
                }
            })

            if (!groupBTNconf.hasClass('d-none')) {
                groupBTNconf.addClass('d-none');
            }

        } else {

            items.each((i, m) => {
                if (m.classList.contains('d-none')) {
                    m.classList.remove('d-none');
                }
            })

            if (groupBTNconf.hasClass('d-none')) {
                groupBTNconf.removeClass('d-none');
            }

        }
    }

    if (selectTurm) {

        selectTurm.each((index, value) => {
            openClosedLi($(value));
        });

        selectTurm.on('change', (e) => {
            openClosedLi($(e.target));
        })

    }
</script>


























{{-- <style>
    table.table-bordered{
    border:1px solid white;
    margin-top:20px;
  }
    table.table-bordered > thead > tr > th{
        border:1px solid white;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid white;
    }
</style>

@php $years = []; (int)$countDiscipline = 0; $countYears = count($dd); @endphp

@if ($user->hasRole('candidado-a-estudante'))
    <div class="row">
        @foreach ($curricularPlanDisciplines as $year => $curricularPlanDisciplines)
        <div class="col-md-6">
        <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
        @php array_push($years, $year); @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
        <select name="classes[{{$year}}]" id="">
            @foreach ($classes as $class)
                @if ($class->year == $year)
                <option value="{{$class->id}}">
                    {{ $class->display_name}}
                </option>
                @endif
            @endforeach
        </select>
            <table class="table table-bordered">
                <thead>
                    <th></th>
                    <th>Cód.</th>
                    <th>Disciplina</th>
                </thead>
            @foreach ($curricularPlanDisciplines as $discipline)
                <tbody>
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id']}}" name="disciplines[{{$discipline->years}}][]" value="{{ $discipline['discipline_id'] }}" data-id="{{ $discipline['discipline_id'] }}" class="check-discipline form-check-input-center" required style="margin-left: 5px;">
                            </div>
                        </td>
                        <td>#{{ $discipline['code']}}</td>
                        <td>{{$discipline['display_name']}}</td>
                    </tr>
                </tbody>
            @endforeach
            </table>
        </div>
        @endforeach
    </div>
    <div hidden>
            <ul>
                <li>
                    @foreach ($years as $year)
                        <input type="text" name="years[]" value="{{ $year }}">
                    @endforeach
                </li>
            </ul>
        </div>

@else
<div class="row">
@foreach ($dd as $year => $dd)
    <div class="col-md-6">
    <h5 class="card-title mb-2">{{ $year }}º Ano</h5>

    <span class="font-weight: bold; margin-right: 6px">Turma:</span>
    @foreach ($dd as $discipline)
        @if (in_array($discipline['discipline_id'], $yz))

        @else
        <select name="classes[{{$year}}]" id="class_{{$year}}" key="{{$year}}">
                @foreach ($classes as $class)
                    @if ($class->year == $year)
                        <option value="{{$class->id}}">
                            {{ $class->display_name }}
                        </option>
                    @endif
                @endforeach
            </select>
            @break
        @endif
    @endforeach


    <table class="table table-bordered">
        <thead>
            <th ></th>
            <th >Cód.</th>
            <th style="border:2px solid #fff; !important">Disciplina</th>
        </thead>
        @foreach ($dd as $discipline)


            <tbody>
                @if (in_array($discipline['discipline_id'], $yz))
                <tr class="table-success">
                    <td style="border:2px solid #fff; !important">

                    </td>
                    <td style="border:2px solid #fff; !important">#{{ $discipline['code']}}</td>
                    <td style="border:2px solid #fff; !important">{{$discipline['display_name']}}</td>
                </tr>
                @else
                    <tr>
                        <td >
                        @php $countDiscipline++; @endphp

                        <div class="custom-control custom-checkbox">
                            <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id']}}" onclick="showSelect({{$discipline['discipline_id']}})" value="{{$discipline['discipline_id']}}" name="disciplines[{{$discipline->years}}][]" value="{{ $discipline['discipline_id'] }}" class="custom-control-input" data-id="{{$discipline['discipline_id']}}" required>
                            @php array_push($years, $year); @endphp
                        </div>
                        </td>
                        <td>#{{ $discipline['code']}}</td>
                        <td>
                            {{$discipline['display_name']}}

                            <div class="checkbox" id="checkbox_group_{{$discipline['discipline_id']}}" hidden style="padding-top: 5px;">
                                <label for="" class="form-check-label" style="color: #595959; margin-left: 20px">
                                    <input type="checkbox" id="checkbox_item_{{$discipline['discipline_id']}}" value="{{ $discipline['discipline_id'] }}" class="check-discipline-regime form-check-input-center" data-id="checkbox_item_{{$discipline['discipline_id']}}" name="disciplines_exam_only[{{$discipline->years}}][]" disabled>
                                    <span>Inscrição para exame</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
            @endforeachDDD  
            

        </table>
        </div>
        @endforeach

        @if ($countDiscipline <= 5)
        @else
        </div>
        @endif

    @if ($countDiscipline <= 5)
    @foreach ($curricularPlanDisciplines as $year => $curricularPlanDisciplines)
        @if ($year < $nextYear)
        <div class="col-md-6">
        <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
        @php array_push($years, $year); @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
        <select name="classes[{{$year}}]" id="">
            @foreach ($classes as $class)
                @if ($class->year == $year)
                <option value="{{$class->id}}">
                    {{ $class->display_name}}
                </option>
                @endif
            @endforeach
        </select>
            <table class="table table-bordered" style="border:2px solid #fff; !important">
                <thead>
                    <th style="border:2px solid #fff; !important"></th>
                    <th style="border:2px solid #fff; !important">Cód.</th>
                    <th style="border:2px solid #fff; !important">Disciplina</th>
                </thead>
            @foreach ($curricularPlanDisciplines as $discipline)
                <tbody>
                    <tr>
                        <td style="border:2px solid #fff; !important">
                            <div class="custom-control custom-checkbox">
                                <input type='checkbox' id="check_discipline_{{ $discipline['discipline_id']}}" name="disciplines[{{$discipline->years}}][]" value="{{ $discipline['discipline_id'] }}" data-id="{{ $discipline['discipline_id'] }}" class="check-discipline form-check-input-center" required style="margin-left: 5px;">
                            </div>
                        </td>
                        <td style="border:2px solid #fff; !important">#{{ $discipline['code']}}</td>
                        <td style="border:2px solid #fff; !important">{{$discipline['display_name']}}</td>
                    </tr>
                </tbody>
            @endforeach
            </table>
        </div>
            @endif
            @endforeach
        </div>
    </div>
            @endif


    <div hidden>
            <ul>
                <li>
                    @foreach ($years as $year)
                        <input type="text" name="years[]" value="{{ $year }}">
                    @endforeach
                </li>
            </ul>
        </div>

        @endif --}}
