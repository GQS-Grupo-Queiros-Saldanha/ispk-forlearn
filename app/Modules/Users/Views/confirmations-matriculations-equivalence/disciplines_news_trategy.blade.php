@if (!empty($DADOS_DISCIPLINA))
    @php
        $years = [];
        $condi = 0;
        $countReprovad = count($disciplinesReproved);
    @endphp

    @if (isset($curricularPlanDisciplines) &&
            count($curricularPlanDisciplines) == 0 &&
            count($DADOS_DISCIPLINA['REPROVADO_ATRASO']) == 0)
        <h2>Sem disciplinas - para ser disponibilizada | Possível finalista</h2>
    @endif


    @foreach ($DADOS_DISCIPLINA['APROVADO_ATRASO'] as $year => $disciplinesReproved)
        <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
        @php array_push($years, $year); @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
        <select name="classes[{{ $year }}]" id="classes[{{ $year }}]" class="select_turm"
            key="{{ $year }}">
            <option value="">Seleciona a turma</option>
            @foreach ($classes as $class)
                @foreach ($disciplinesReproved as $validarCurso)
                    @if ($class->year == $year && $class->courses_id == $validarCurso['courses_id'])
                        <option value="{{ $class->id }}">
                            {{ $class->display_name }}
                        </option>
                    @endif
                @endforeach
            @endforeach

        </select>

        <ul style="list-style: none">
            @foreach ($DADOS_DISCIPLINA['APROVADO_ATRASO'] as $yearR => $discipline)
                @foreach ($discipline as $data)
                    <li class="item_classes_{{ $yearR }}">

                        @if ($year == $yearR)
                            <input type="checkbox" id="check_discipline_{{ $data['discipline_id'] }}"
                                value="{{ $data['discipline_id'] }}" name="disciplines[{{ $data->years }}][]"
                                class="check-discipline form-check-input-center" data-id="{{ $data['discipline_id'] }}"
                                required>
                            <label for="check_discipline_{{ $data['discipline_id'] }}">
                                #{{ $data['code'] }}-{{ $data['display_name'] }}
                            </label>
                            <br>
                            <div class="checkbox" id="checkbox_group_{{ $data['discipline_id'] }}" hidden>
                                <label for="" class="form-check-label" style="color:#595959; margin-left: 20px">
                                    <input type="checkbox" id="checkbox_item_{{ $data['discipline_id'] }}"
                                        value="{{ $data['discipline_id'] }}"
                                        class="check-discipline-regime form-check-input-center"
                                        data-id="checkbox_item_{{ $data['discipline_id'] }}"
                                        name="disciplines_exam_only[{{ $data->years }}][]" disabled>
                                    <span>Inscrição para exame</span>
                                </label>
                            </div>
                        @endif

                    </li>
                @endforeach
            @endforeach
        </ul>
    @endforeach

    @foreach ($DADOS_DISCIPLINA['REPROVADO_ATRASO'] as $year => $disciplinesReprovedR)
        <h5 class="card-title mb-2">{{ $year }}º Ano</h5>
        @php $years[] = $year; @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
        <select name="classes[{{ $year }}]" id="classes[{{ $year }}]" class="select_turm"
            key="{{ $year }}">
            <option value="">Seleciona a turma</option>
            @foreach ($classes as $class)
                @if (
                    $class->year == $year &&
                        $class->courses_id == $disciplinesReprovedR[0]['courses_id'] &&
                        isset($disciplinesReprovedR[0]['courses_id']))
                    <option value="{{ $class->id }}">
                        {{ $class->display_name }}
                    </option>
                @endif
            @endforeach
        </select>

        <ul style="list-style: none">
            @foreach ($DADOS_DISCIPLINA['REPROVADO_ATRASO'] as $yearR => $discipline)
                @foreach ($discipline as $data)
                    <li class="item_classes_{{ $yearR }}">

                        @if ($year == $yearR)
                            <input type="checkbox" id="check_discipline_{{ $data['discipline_id'] }}"
                                value="{{ $data['discipline_id'] }}" name="disciplines[{{ $data->years }}][]"
                                class="check-discipline form-check-input-center" data-id="{{ $data['discipline_id'] }}"
                                required >
                            <label for="check_discipline_{{ $data['discipline_id'] }}">
                                #{{ $data['code'] }}-{{ $data['display_name'] }}
                            </label>
                            <br>
                            <div class="checkbox" id="checkbox_group_{{ $data['discipline_id'] }}" hidden>
                                <label for="" class="form-check-label" style="color:#595959; margin-left: 20px">
                                    <input type="checkbox" id="checkbox_item_{{ $data['discipline_id'] }}"
                                        value="{{ $data['discipline_id'] }}"
                                        class="check-discipline-regime form-check-input-center"
                                        data-id="checkbox_item_{{ $data['discipline_id'] }}"
                                        name="disciplines_exam_only[{{ $data->years }}][]" disabled>
                                    <span>Inscrição para exame</span>
                                </label>
                            </div>
                        @endif


                    </li>
                @endforeach
            @endforeach
        </ul>
    @endforeach


    {{-- {{$estado->last()}}     verificador de aprovação --}}
    @if (
        (isset($curricularPlanDisciplines) && count($curricularPlanDisciplines) > 0 && $estado->last() == 'Aprovado') ||
            ($estado->last() == '' && count($curricularPlanDisciplines) > 0))


        @php
            $yearRPlan = $curricularPlanDisciplines->keys()->first();
            $firstDisciplineNextYear = $curricularPlanDisciplines->first();
        @endphp

        <h5 class="card-title mb-2">{{ $yearRPlan }}º Ano</h5>
        @php $years[] = $yearRPlan; @endphp
        <span class="font-weight: bold; margin-right: 6px">Turma:</span>
        <select name="classes[{{ $yearRPlan }}]" id="classes[{{ $yearRPlan }}]" class="select_turm"
            key="{{ $yearRPlan }}">
            <option value="">Seleciona a turma</option>
            @foreach ($classes as $class)
                @if ($class->year == $yearRPlan)
                    <option value="{{ $class->id }}">
                        {{ $class->display_name }}
                    </option>
                @endif
            @endforeach
        </select>
        <ul style="list-style: none">
            <input type="hidden" name="Aprovado" value="{{ $yearRPlan }}">
            @foreach ($firstDisciplineNextYear as $data)
                <li class="item_classes_{{ $yearRPlan }}">
                    <input type="checkbox" id="check_discipline_{{ $data['discipline_id'] }}"
                        value="{{ $data['discipline_id'] }}" name="disciplines[{{ $data->years }}][]"
                        class="check-discipline form-check-input-center" data-id="{{ $data['discipline_id'] }}"
                        required checked>
                    <label for="check_discipline_{{ $data['discipline_id'] }}">
                        #{{ $data['code'] }}-{{ $data['display_name'] }}
                    </label>
                    <br>
                    <div class="checkbox" id="checkbox_group_{{ $data['discipline_id'] }}" hidden>
                        <label for="" class="form-check-label" style="color:#595959; margin-left: 20px">
                            <input type="checkbox" id="checkbox_item_{{ $data['discipline_id'] }}"
                                value="{{ $data['discipline_id'] }}"
                                class="check-discipline-regime form-check-input-center"
                                data-id="checkbox_item_{{ $data['discipline_id'] }}"
                                name="disciplines_exam_only[{{ $data->years }}][]" disabled>
                            <span>Inscrição para exame</span>
                        </label>
                    </div>

                </li>
            @endforeach
        </ul>
    @endif








    <div hidden>
        <ul>
            <li>
                @php
                    count($years);
                @endphp

                @foreach ($years as $year)
                    <input type="text" name="years[]" value="{{ $year }}">
                @endforeach

            </li>
        </ul>
    </div>
@else
    <h5 class="card-title mb-2">Sem disciplinas</h5>
@endif


<script>
    var selectTurm = $('.select_turm');

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
