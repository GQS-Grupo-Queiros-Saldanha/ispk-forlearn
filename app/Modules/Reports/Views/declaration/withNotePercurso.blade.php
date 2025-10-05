@extends('layouts.print')
@section('title', __('Declaração com notas'))
@section('content')

<style>
    body {
        font-family: 'Tinos', serif;
        padding: 0;
        margin: 0;
    }

    .table td,
    .table th {
        padding: 0;
        border: 0;
    }

    .form-group, .card, label {
        display: block !important;
    }

    .form-group {
        margin-bottom: 1px;
        font-weight: normal;
        line-height: unset;
        font-size: 0.75rem;
    }

    .h1-title,
    .h1-title_Com {
        padding: 0;
        margin-bottom: 0;
        font-size: 4.3em;
    }

    .img-institution-logo {
        width: 50px;
        height: 50px;
    }

    .img-parameter {
        max-height: 100px;
        max-width: 50px;
    }

    .table-parameter-group {
        page-break-inside: avoid;
    }

    .table-parameter-group td,
    .table-parameter-group th {
        vertical-align: unset;
    }

    .tbody-parameter-group {
        border-top: 0;
        padding: 0;
        margin: 0;
    }

    .thead-parameter-group {
        color: white;
        background-color: #3D3C3C;
    }

    .th-parameter-group {
        padding: 2px 5px !important;
        font-size: .625rem;
    }

    .div-top {
        height: 99px;
        position: relative;
        margin-bottom: 15px;
        background-color: rgb(240, 240, 240);
        background-image: url('https://forlearn.ao/storage/attachment/{{ $institution->logotipo }}');
        background-position: 100%;
        background-repeat: no-repeat;
        background-size: 10%;
    }

    .td-institution-name {
        vertical-align: middle !important;
        font-weight: bold;
        text-align: justify;
    }

    .td-institution-logo {
        vertical-align: middle !important;
        text-align: center;
    }

    .td-parameter-column {
        padding-left: 5px !important;
    }

    label {
        font-weight: bold;
        font-size: .75rem;
        color: #000;
        margin-bottom: 0;
    }

    input, textarea, select {
        display: none;
    }

    .td-fotografia {
        background-size: cover;
        padding-left: 10px !important;
        padding-right: 10px !important;
        width: 85px;
        height: 100%;
        margin-bottom: 5px;
        background-position: 50%;
        margin-right: 8px;
    }

    .mediaClass td {
        border: 1px solid #fff;
    }

    p {
        @if($config->tamanho_fonte != "")
            font-size: {{ $config->tamanho_fonte }}pt;
        @else
            font-size: 1.5rem;
        @endif
        margin-left: 120px !important;
        margin-right: 120px !important;
        color: black;
        text-align: justify;
    }

    .dados_pessoais {
        margin-bottom: -5px;
    }

    .pl-1 {
        padding-left: 1rem !important;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }

    tfoot {
        display: table-footer-group;
    }

    .bg0 {
        background-color: #2f5496 !important;
    }

    .bg1 {
        background-color: #8eaadb !important;
    }

    .bg2 {
        background-color: #d9e2f3 !important;
    }

    .bg3 {
        background-color: #fbe4d5 !important;
    }

    .bg4 {
        background-color: #f4b083 !important;
    }

    .table_te td {
        font-size: 10pt !important;
    }

    .body {
        margin-top: -50px;
    }

    .div-top {
        margin-bottom: -40px !important;
    }

    .institution-name {
        margin-left: 90px !important;
    }

    /* Estilos específicos da tabela */
    .table_te {
        margin-left: 120px !important;
        margin-right: 120px !important;
        text-align: right;
        margin-bottom: 6px;
        font-size: {{ $config->tamanho_fonte ?? 'inherit' }}pt;
    }

    .cor_linha {
        background-color: #999;
        color: #000;
    }

    .table_te th {
        border: 1px solid rgb(238, 233, 233) !important;
        padding: 4px !important;
        text-align: center;
    }

    .table_te td {
        border: 1px solid rgb(238, 233, 233) !important;
        font-size: 14pt;
    }
</style>

<main>
    @include('Reports::declaration.cabecalho.cabecalho_forLEARN')

    @php
        $status = $status == 0 ? "concluiu o" : "a frequentar";
        $province = matchProvince(substr($studentInfo->bi, -5, 2));
        
        // Encontrar ano académico
        $academic_year = '';
        foreach ($disciplines as $discipline) {
            foreach ($oldGrades as $year => $oldGradex) {
                foreach ($oldGradex as $oldGrade) {
                    if ($oldGrade->discipline_id == $discipline->id) {
                        $academic_year = $oldGrade->lective_year;
                        break 2;
                    }
                }
            }
            if ($academic_year) break;
        }
    @endphp

    <div class="body">
        <p>
            {{ $direitor->grau_academico ?? 'Grau Académico' }}, 
            <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b>,
            {{ $direitor->cargo ?? 'cargo' }} do <b>{{ $institution->nome }}</b>, 
            declara para os devidos efeitos, que 
            <b>{{ $studentInfo->name }}</b>, 
            filho(a) de {{ $studentInfo->dad }} e de {{ $studentInfo->mam }}, 
            nascido(a) aos {{ $nascimento }}, 
            portador(a) do B.I nº {{ $studentInfo->bi }}, 
            passado pelo Arquivo de Identificação de {{ $province }}, 
            aos {{ \Carbon\Carbon::parse($studentInfo->emitido)->format('d/m/Y') }},
            {{ $status }} <b>{{ $ano }}º Ano</b>, 
            no ano académico {{ $academic_year ?? 'Ano lectivo' }}, 
            no curso de Licenciatura em <b>{{ $studentInfo->course }}</b>, 
            com a Matrícula nº <b>{{ $studentInfo->number }}</b>, 
            tendo obtido as seguintes classificações:
        </p>

        <div class="row">
            <div class="col-12">
                <div class="">
                    @php
                        $i = 1;
                        $areaGeral = 0; $contaGeral = 0;
                        $areaEspecifia = 0; $contaEspecifica = 0;
                        $areaProfissional = 0; $contaProfissional = 0;
                        $notas = 0; $count_notas = 0;
                    @endphp

                    <table class="table_te" style="width: 76%; margin-top: -20px">
                        <tr style="font-weight: 900 !important">
                            <th class="bg1" style="text-align: center; font-size: 15pt;"><b>#</b></th>
                            <th class="bg1" style="text-align: center; font-size: 15pt;">CÓDIGO</th>
                            <th class="bg1" style="text-align: left; font-size: 15pt; text-indent: 10px;"><b>DISCIPLINAS</b></th>
                            <th class="bg1" style="text-align: center; font-size: 15pt;"><b>HORAS</b></th>
                            <th class="bg1" style="text-align: center; font-size: 15pt;"><b>UC</b></th>
                            
                            @foreach ($oldGrades as $year => $oldGrade)
                                @if(!empty($oldGrade))
                                    <th class="bg1" style="text-align: center; font-size: 15pt;" colspan="1"><b>CLASSIFICAÇÃO</b></th>
                                    @break
                                @endif
                            @endforeach

                            @if ($var == 1)
                                @foreach ($studyPlanEditions as $studyPlanEdition)
                                    @if(!empty($studyPlanEdition))
                                        <th class="bg1" style="text-align: center; font-size: 15pt;" colspan="1"><b>CLASSIFICAÇÃO</b></th>
                                        @break
                                    @endif
                                @endforeach
                            @endif
                        </tr>
                       
                        @foreach ($disciplines as $discipline)
                            @php
                                $cor = $i % 2 === 0 ? 'cor_linha' : '';
                                $i++;
                            @endphp
                            
                            <tr class="{{ $cor }}">
                                <td class="bg2" style="text-align: center;">{{ $i-1 }}</td>
                                <td class="bg2" style="text-align: left; text-indent: 5px">{{ $discipline->code }}</td>
                                <td class="bg2" style="text-align: left; text-indent: 5px">{{ $discipline->name }}</td>
                                
                                @php $cargaEncontrada = false; @endphp
                                @foreach($cargaHoraria as $carga)
                                    @if($discipline->id === $carga->id_disciplina)
                                        <td class="bg2" style="text-align: center;">{{ $carga->hora }}</td>
                                        @php $cargaEncontrada = true; @endphp
                                        @break
                                    @endif
                                @endforeach
                                @if(!$cargaEncontrada)
                                    <td class="bg2" style="text-align: center;">-</td>
                                @endif

                                <td class="bg2" style="text-align: center;">{{ $discipline->uc ?? '-' }}</td>

                                @foreach ($oldGrades as $year => $oldGradex)
                                    @php $notaEncontrada = false; @endphp
                                    @foreach ($oldGradex as $oldGrade)
                                        @if ($oldGrade->discipline_id == $discipline->id)
                                            @php $notaEncontrada = true; @endphp
                                            
                                            @if ($discipline->area_id == 13)
                                                @php $areaGeral += $oldGrade->grade; $contaGeral++; @endphp
                                            @elseif($discipline->area_id == 14)
                                                @php $areaProfissional += $oldGrade->grade; $contaProfissional++; @endphp
                                            @elseif($discipline->area_id == 15)
                                                @php $areaEspecifia += $oldGrade->grade; $contaEspecifica++; @endphp
                                            @endif
                                            
                                            <td class="bg2" style="text-align: center;">{{ round($oldGrade->grade) }}</td>
                                            @php $notas += $oldGrade->grade; $count_notas++; @endphp
                                            @break
                                        @endif
                                    @endforeach
                                    
                                    @if(!$notaEncontrada)
                                        <td class="bg2" style="text-align: center;">-</td>
                                    @endif
                                @endforeach
                                
                                @if ($var == 1)
                                    @php $notaAtualEncontrada = false; @endphp
                                    @foreach ($grades as $grade)
                                        @if ($grade->discipline_id == $discipline->id)
                                            @php $notaAtualEncontrada = true; @endphp
                                            @php $notaFinal = round($grade->percentage_mac + $grade->percentage_neen); @endphp
                                            
                                            @if ($discipline->area_id == 13)
                                                @php $areaGeral += $notaFinal; $contaGeral++; @endphp
                                            @elseif($discipline->area_id == 14)
                                                @php $areaProfissional += $notaFinal; $contaProfissional++; @endphp
                                            @elseif($discipline->area_id == 15)
                                                @php $areaEspecifia += $notaFinal; $contaEspecifica++; @endphp
                                            @endif
                                            
                                            <td class="bg2" style="text-align: center;">{{ $notaFinal }}</td>
                                            @php $notas += $notaFinal; $count_notas++; @endphp
                                            @break
                                        @endif
                                    @endforeach
                                    
                                    @if(!$notaAtualEncontrada)
                                        <td class="bg2" style="text-align: center;">-</td>
                                    @endif
                                @endif
                            </tr>
                        @endforeach
                        
                        @if ($status_finalist == 1 && !empty($final_note))
                            <tr>
                                <td class="td bg4" style="text-align: center !important;">{{ count($disciplines) + 1 }}</td>
                                <td class="td bg4" style="text-align: left;">{{ $final_note[0]->display_name ?? 'Trabalho Final' }}</td>
                                <td class="td bg4" style="text-align: center;">64</td>
                                <td class="td bg4" style="text-align: center;" colspan="2">{{ round($final_note[0]->grade) ?? '-' }}</td>
                            </tr>
                        @endif
                        
                        @if ($status_finalist == 2)
                            <tr>
                                <td class="td bg4" style="text-align: center !important;">{{ count($disciplines) + 1 }}</td>
                                <td class="td bg4" style="text-align: left;">Trabalho de Fim de Curso</td>
                                <td class="td bg4" style="text-align: center;">64</td>
                                <td class="td bg4" style="text-align: center;" colspan="2">F</td>
                            </tr>
                        @endif
                        
                        @if($count_notas > 0)
                            @php $average = $notas / $count_notas; @endphp
                            <tr>
                                <td class="td bg4" style="text-align: center !important;" colspan="5"><b>MÉDIA</b></td>
                                <td class="td bg4" style="text-align: center;">{{ round($average) }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        
        <br>
        
        <div>
            <p style="font-size: 14pt !important;">
                @if ($status_finalist == 2)
                    Obs: O estudante ainda não terminou o curso, pois falta-lhe a apresentação e defesa do Trabalho de Fim de Curso.
                @endif
                Por ser verdade e me ter sido solicitada, passa-se a presente declaração nº {{ $requerimento->code ?? 'código doc' }}, 
                liquidada no CP nº {{ $recibo ?? 'recibo' }}, assinada e autenticada com o carimbo a óleo em uso no {{ $institution->abrev }}.
            </p>
            
            <p style="font-size: 16pt !important; text-align: left; font-weight: bolder !important; margin-bottom: 40px !important; margin-top: -10px">
                {{ $institution->provincia }}, aos {{ $dataActual }}
            </p>
            
            <p>_______________________________________</p>
            <p style="font-size: 14pt !important; margin-top: -18px !important;">
                {{ $direitor->grau_academico ?? 'Grau Académico' }}, 
                <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b>
            </p>
            <p style="font-size: 10pt !important; margin-top: -24px">
                {{ $direitor->categoria_profissional ?? 'Categoria Profissional' }}
            </p>
            <p style="font-size: 10pt !important; margin-top: -22px">
                {{ $direitor->cargo ?? 'Cargo' }} do {{ $institution->abrev }}
            </p>
        </div>

        <div class="watermark"></div>
    </div>
</main>

@endsection