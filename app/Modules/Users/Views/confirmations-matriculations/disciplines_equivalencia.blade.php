@php
    $years = [];
    $countDiscipline = 0;
    $especialidade = [];
    $basta = 0;
    $condi = 0;
    $countReprovad = count($disciplinesReproved);
@endphp

@if (isset($estado['error']) && $estado['error'] === "yes")

    <h3>Atenção!</h3><br>
    <div class="alert-warning p-3">
        <p>
            A <b>forLEARN</b> detectou que este é um estudante vindo por equivalência.
            Existem parâmetros que impedem a matrícula.
        </p>
        <br>
        <label>1º Lançou todas as notas positivas?</label><br>
        <label>2º As notas são todas positivas?</label><br>
        <label>3º Atualizou notas após adicionar disciplinas?</label><br>
        <label>4º Houve mudança de curso após lançamento?</label>
    </div>

@elseif(!empty($disciplinesReproved) || !empty($curricularPlanDisciplines))

    {{-- DISCIPLINAS EM ATRASO --}}
    @foreach ($disciplinesReproved as $year => $disciplinasAno)

        <h5>{{ $year }}º Ano</h5>
        @php $years[] = $year; @endphp

        <label><b>Turma:</b></label>
        <select name="classes[{{$year}}]" class="select-turma">
            <option value="">Seleciona a turma</option>
            @foreach ($classes as $class)
                @foreach ($disciplinasAno as $d)
                    @if ($class->year == $year && $class->courses_id == $d['courses_id'])
                        <option value="{{ $class->id }}">
                            {{ $class->display_name }}
                        </option>
                        @break
                    @endif
                @endforeach
            @endforeach
        </select>

        <ul style="list-style:none">
            @foreach ($disciplinasAno as $discipline)
                @php $countDiscipline++; @endphp
                <li>
                    <input type="checkbox"
                           id="check_discipline_{{ $discipline['discipline_id'] }}"
                           onclick="showSelect({{ $discipline['discipline_id'] }})"
                           name="disciplines[{{$discipline->years}}][]"
                           value="{{ $discipline['discipline_id'] }}"
                           class="check-discipline">

                    <label>
                        #{{ $discipline['code'] }} - {{ $discipline['display_name'] }}
                    </label>

                    <div id="checkbox_group_{{ $discipline['discipline_id'] }}" hidden>
                        <label style="margin-left:20px">
                            <input type="checkbox"
                                   id="checkbox_item_{{ $discipline['discipline_id'] }}"
                                   name="disciplines_exam_only[{{$discipline->years}}][]"
                                   disabled>
                            Inscrição para exame
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    @endforeach

    {{-- PRÓXIMO ANO --}}
    <div id="ContainerSeguinte">

        @foreach ($curricularPlanDisciplines as $year => $disciplinasAno)
            @if ($year <= $nextYear)

                <h5>{{ $year }}º Ano</h5>
                @php $years[] = $year; @endphp

                <label><b>Turma:</b></label>
                <select name="classes[{{$year}}]" class="select-turma">
                    <option value="">Seleciona a turma</option>
                    @foreach ($classes as $class)
                        @foreach ($disciplinasAno as $d)
                            @if ($class->year == $year && $class->courses_id == $d['courses_id'])
                                <option value="{{ $class->id }}">
                                    {{ $class->display_name }}
                                </option>
                                @break
                            @endif
                        @endforeach
                    @endforeach
                </select>

                {{-- ESPECIALIDADE CEE --}}
                @if($estado['curso']==="CEE" && ($year===3 || $year===4))
                    <br>
                    <label><b>Especialidade:</b></label>
                    <select id="espcialidadeCEE">
                        <option value="">Seleciona</option>
                        <option value="COA">COA</option>
                        <option value="GEE">GEE</option>
                        <option value="ECO">ECO</option>
                    </select>
                @endif

                <ul class="Lista_disciplina" style="list-style:none">
                    @foreach ($disciplinasAno as $discipline)
                        @php
                            $especialidade[] = [
                                'codigo' => $discipline['code'],
                                'id_disciplina' => $discipline['discipline_id']
                            ];
                        @endphp
                        <li class="item_list_disciplines_{{ $discipline['discipline_id'] }}">
                            <input type="checkbox"
                                   checked
                                   name="disciplines[{{$discipline->years}}][]"
                                   value="{{ $discipline['discipline_id'] }}">
                            #{{ $discipline['code'] }} - {{ $discipline['display_name'] }}
                        </li>
                    @endforeach
                </ul>

                @break
            @endif
        @endforeach

    </div>

@endif
<script>
function showSelect(id) {
    if ($("#check_discipline_" + id).is(':checked')) {
        $("#checkbox_group_" + id).prop('hidden', false);
        $("#checkbox_item_" + id).prop('disabled', false);
    } else {
        $("#checkbox_group_" + id).prop('hidden', true);
        $("#checkbox_item_" + id).prop('disabled', true);
    }
}

// ===============================
// CONTROLO DO BOTÃO POR TURMA
// ===============================
function controlarBotaoPorTurma() {
    let mostrar = true;

    $('.select-turma').each(function () {
        if ($(this).val() === "") {
            mostrar = false;
        }
    });

    if (mostrar) {
        $('#groupBTNconf').removeAttr('hidden').show();
    } else {
        $('#groupBTNconf').hide();
    }
}

controlarBotaoPorTurma();

$(document).on('change', '.select-turma', function () {
    controlarBotaoPorTurma();
});
</script>
