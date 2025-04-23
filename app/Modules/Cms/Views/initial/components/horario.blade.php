<style>
    .cell-forlearn {
        padding: 5px;
        padding-left: 10px;
        padding-right: 10px;
        margin: 0px;
        border-radius: 2px;
        border: 6px solid #8eaadb;
        border-radius: 7px;
        font-size: 9px; 
        font-weight: bold;
        text-align: left;
        vertical-align: top;

    }

    .cell-forlearn-top {
        font-size: 9px;
    }

    .bg0 {
        background-color: #2f5496 !important;
        color: white;
    }

    .bg1 {
        background-color: #8eaadb !important;
    }

    .bg2 {
        background-color: #d9e2f3 !important;
    }

    .bg3 {
        background-color: #fc8a17 !important;
    }

    .bg4 {
        /* background-color: #00c0ef !important; */
        background-color: #d9e2f3 !important;
        padding: 5px;
        /* border-left: 6px solid #d9e2f3;
        border-right: 6px solid #d9e2f3; */

    }

    .table-forlearn {
        font-size: 12px;
    }

    .img-forlearn {
        height: 13px;
        width: 75px;
    }

    .span-forlearn{
        position: absolute;
        bottom: 0;
        right: 0;
        background-color: #2f5496;
        color: white;
        padding: 2px 5px;
    }
</style>

@if (is_object($horario))
    @foreach ($horario as $key => $item)
        <table class="table-forlearn">

            <thead>
                @if (is_object($horario))
                    @foreach ($horario as $key => $item)
                        @if (isset($item['classes']))
                            <tr>
                                <td class="cell-forlearn-top"><img class="img-forlearn"
                                        src="https://dev.forlearn.ao/img/login/ForLEARN 03.png" title="Logo forLEARN"
                                        alt="Logo forLEARN"></td>
                                <td class="cell-forlearn-top"></td>

                            </tr>
                            <tr>
                                <td class="cell-forlearn-top bg4" colspan="2">Curso:
                                    <b>{{ $item['classes']->curso }}</b></td>
                                <td class="cell-forlearn-top bg4">Turma: <b>{{ $item['classes']->code }}</b></td>
                                <td class="cell-forlearn-top bg4">Turno: <b>{{ $item['classes']->turno }}</b></td>
                                <td class="cell-forlearn-top bg4" colspan="2">Simestre: <b>{{ $key }}</b>
                                </td>
                            </tr>
                        @else
                        @endif
                    @endforeach
                @else
                @endif
                <tr>
                    {{-- <th class="cell-forlearn bg0">#</th> --}}
                    <th class="cell-forlearn bg3" style="text-align: center;">Hora</th>
                    <th class="cell-forlearn bg0" style="text-align: center;">Segunda<br>Feira</th>
                    <th class="cell-forlearn bg0" style="text-align: center;">Terça<br>Feira</th>
                    <th class="cell-forlearn bg0" style="text-align: center;">Quarta<br>Feira</th>
                    <th class="cell-forlearn bg0" style="text-align: center;">Quinta<br>Feira</th>
                    <th class="cell-forlearn bg0" style="text-align: center;">Sexta<br>Feira</th>
                </tr>
            </thead>



            @php
                $i = 0;
                $count = [0, 1, 2, 3, 4, 5];
            @endphp

            @foreach ($count as $index)
                <tr>
                    {{-- <td class="cell-forlearn bg0" >{{ $index + 1 }}</td> --}}
                    <td class="cell-forlearn bg3" style="text-align: center;width: 300px!important;">
                        @if (isset($tempo[$index]->start))
                            {{ $tempo[$index]->start }}
                        @endif
                        <br>
                        @if (isset($tempo[$index]->end))
                            {{ $tempo[$index]->end }}
                        @endif
                    </td>
                    <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                        @if (isset($item['segunda'][$index]))
                            {{ $item['segunda'][$index] }}
                        @endif
                        @if (isset($item['segunda_room'][$index]))
                            <span class="span-forlearn">{{ $item['segunda_room'][$index] }}</span>
                        @endif
                    </td>
                    <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                        @if (isset($item['terca'][$index]))
                            {{ $item['terca'][$index] }}
                        @endif
                        @if (isset($item['terca_room'][$index]))
                            <span class="span-forlearn">{{ $item['terca_room'][$index] }}</span>
                        @endif
                    </td>
                    <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                        @if (isset($item['quarta'][$index]))
                            {{ $item['quarta'][$index] }}
                        @endif
                        @if (isset($item['quarta_room'][$index]))
                            <span class="span-forlearn">{{ $item['quarta_room'][$index] }}</span>
                        @endif
                    </td>
                    <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                        @if (isset($item['quinta'][$index]))
                            {{ $item['quinta'][$index] }}
                        @endif
                        @if (isset($item['quinta_room'][$index]))
                            <span class="span-forlearn">{{ $item['quinta_room'][$index] }}</span>
                        @endif
                    </td>
                    <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                        @if (isset($item['sexta'][$index]))
                            {{ $item['sexta'][$index] }}
                        @endif
                        @if (isset($item['sexta_room'][$index]))
                            <span class="span-forlearn">{{ $item['sexta_room'][$index] }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach

            <tbody>

            </tbody>
        </table>
        <br><br>
    @endforeach
@endif
@if (!is_object($horario))
    <div class="alert alert-warning text-dark font-bold">
        {{ $horario }}</div>
@endif
