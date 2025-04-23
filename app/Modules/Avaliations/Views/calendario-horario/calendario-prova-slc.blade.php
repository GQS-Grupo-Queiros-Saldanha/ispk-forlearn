@php
    use App\Modules\Avaliations\util\CalendarioProvaHorarioUtil;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marcação de prova</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            font-size: 12pt;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: sans-serif;
        }

        .panel-calendario {
            padding: 2rem;
        }

        .panel-calendario:not(:first-child) {
            margin-top: 2rem;
        }

        .titulos {
            text-align: center;
        }

        .titulos div:not(:last-child) {
            padding-bottom: 0.5rem;
        }

        .cabecalho .img-logo img {
            width: 90px;
            height: 90px;
        }

        .cabecalho .img-logo {
            position: absolute;
            left: 12%;
        }

        .cabecalho {
            display: flex;
            position: relative;
            width: max-content;
        }

        .cabecalho div:nth-child(2) div:last-child {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .identificao {
            margin-bottom: 0.5rem;
            width: 100%;
            height: max-content;
        }

        .identificao p {
            position: relative;
        }

        .identificao p span {
            width: 220px;
        }

        .identificao p .periodo-ano {
            position: absolute;
            right: 40%;
        }

        .identificao p .room {
            position: absolute;
            right: 0%;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 0.5rem;
        }

        thead {
            background: #c3d291;
            text-align: center;
        }

        td {
            text-align: center;
            background: rgba(249, 242, 244, 1);
        }

        th {
            background: rgba(240, 240, 240, 1);
        }

        td,
        th {
            border: 0.5px solid white;
            padding: 0.2rem;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .bold-300 {
            font-weight: 300;
        }

        .p-top {
            padding-top: 0.8rem;
        }

        .p-top-min {
            padding-top: 0.4rem;
        }

        .calendar-date {
            margin-top: 1.2rem;
            margin-left: 2rem;
        }

        .assinatura {
            width: 90%;
            margin-left: 50px;
        }

        .assinatura p {
            position: relative;
            margin-top: 2.6rem;
        }

        .assinatura p span {
            text-align: center;
            border-bottom: 1px solid;
            padding-bottom: 1.7rem;
            width: 200px;
        }

        .assinatura p span:first-child {
            position: absolute;
            left: 30;
        }

        .assinatura p span:last-child {
            position: absolute;
            right: 0;
        }

        .toUpperCase {
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div>
        @isset($horarioProvas)
            @foreach ($horarioProvas as $key => $marcacaoProva)
                <section class="panel-calendario">
                    <header class="cabecalho">
                        <div class="img-logo">
                            <img src="{{ url('https://' . $_SERVER['HTTP_HOST'] . '//storage//' . $institution->logotipo) }}"
                                alt="">
                        </div>
                        <div class="titulos">
                            <div class="toUpperCase">{{ $institution->nome }}</div>
                            <div>DEPARTAMENTO DOS ASSUNTOS ACADÉMICOS</div>
                            <div>DEPARTAMENTO DA CIÊNCIAS DE EDUCAÇÃO</div>
                            @php $simes = CalendarioProvaHorarioUtil::describeNumberRomano($marcacaoProva->simestre); @endphp
                            <div>CALENDÁRIO DE EXAMES E RECURSOS DO {{ $simes }}º SEMESTRE/{{ date('Y') }}</div>
                        </div>
                    </header>
                    <div>
                        <div class="identificao">
                            <p>
                                <span class="toUpperCase">CURSO:
                                    {{ $marcacaoProva->curso->currentTranslation->display_name }}</span>
                                <span class="periodo-ano order-3">{{ $marcacaoProva->anoCurricular }}º ANO - PERIÓDO :
                                    {{ CalendarioProvaHorarioUtil::describePeriodo($key) }}</span>
                                <span class="room">SALA: {{ $marcacaoProva->turmaSala->sala }}</span>
                            </p>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th class="text-center" rowspan="2">Nº</th>
                                    <th class="p-top" rowspan="2">DISCIPLINAS</th>
                                    <th class="p-top" colspan="2">EXAME NORMAL</th>
                                    <th rowspan="2">HORA DE <br /> INICIO</th>
                                    <th class="p-top" colspan="2">EXAME DE RECURSO</th>
                                    <th rowspan="2">HORA DE <br /> INICIO</th>
                                    <th class="p-top" rowspan="2">JURI</th>
                                </tr>
                                <tr>
                                    <th class="p-top-min">Datas</th>
                                    <th class="p-top-min">Dias</th>
                                    <th class="p-top-min">Datas</th>
                                    <th class="p-top-min">Dias</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($marcacaoProva->horarios as $prova)
                                    <tr>
                                        <td class="bold-300 text-center">{{ $loop->index + 1 }}ª</td>
                                        <td class="text-left">{{ $prova->disciplina }}</td>
                                        <td class="text-center">{{ (new DateTime($prova->data_marcada))->format('d/m/Y') }}
                                        </td>
                                        @isset($prova->data_marcada)
                                            <td class="text-center">
                                                {{ CalendarioProvaHorarioUtil::describeDataMarcada($prova->data_marcada) }}
                                            </td>
                                        @else
                                            <td>-</td>
                                        @endisset
                                        <td>{{ $prova->intervalo }}</td>
                                        @php
                                            $recurso = CalendarioProvaHorarioUtil::getRecursoProva($prova->disciplina_id, $prova->lectiveYear, $key);
                                        @endphp
                                        <td class="text-center">{{ isset($recurso->data_marcada) ? (new DateTime($recurso->data_marcada))->format('d/m/Y') : '-' }}</td>
                                        @isset($recurso->data_marcada)
                                            <td class="text-center">
                                                {{ CalendarioProvaHorarioUtil::describeDataMarcada($recurso->data_marcada) }}
                                            </td>
                                        @else
                                            <td class="text-center">-</td>
                                        @endisset
                                        <td>{{ $recurso->intervalo ?? '-' }}</td>
                                        <td class="toUpperCase text-left">
                                            {{ CalendarioProvaHorarioUtil::describeJuris($prova->calendario_horario_id,$key) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="calendar-date toUpperCase">Benguela, {{ date('d') }}
                            {{ CalendarioProvaHorarioUtil::getMesAtual() }} de {{ date('Y') }}</div>
                        @if ($loop->index == 0)
                            <div class="assinatura">
                                <p>
                                    <span>O DACC</span>
                                    <span>O VIDACC</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </section>
            @endforeach
        @endisset
    </div>
</body>

</html>
