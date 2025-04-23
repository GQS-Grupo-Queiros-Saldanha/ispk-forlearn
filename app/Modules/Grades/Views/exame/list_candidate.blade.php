@extends('layouts.print')
@section('title', __('Candidatos admitidos'))
@section('content')

 
 
    
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'CANDIDATOS ADMITIDOS A EXAME DE ACESSO';
        $discipline_code = '';
    @endphp

    <main>
        @include('Reports::pdf_model.forLEARN_header') 
        
        <style>
        thead>*{
            font-size: 15px!important;
        }
    </style>
        <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-12 mb-4">
                        <table class="table_te">


                            <tr class="bg1">
                                <th class="text-center">Ano lectivo</th>
                                <th class="text-center">Fase</th>
                                <th class="text-center">Curso</th>
                                <th class="text-center">Turma</th>
                                <th class="text-center">Total candidatos</th>
                            </tr>
                            <tr class="bg2">
                                <td class="text-center bg2">
                                    @foreach ($model as $anoLectivo)
                                        {{ $anoLectivo->lective_year_code }}
                                    @break
                                @endforeach
                            </td>
                            <td class="text-center bg2">
                                @foreach ($model as $anoLectivo)
                                    {{ isset($lectiveCandidate->fase) ? $lectiveCandidate->fase : '' }}
                                @break
                            @endforeach
                        </td>

                        <td class="text-center bg2">{{ $curso }}</td>

                        <td class="text-center bg2">{{ $turmaC }}</td>

                        @php
                            $count = 0;
                        @endphp

                        @foreach ($model as $curso)
                            @if ($curso->state == 'total')
                                @php
                                    $count++;
                                @endphp
                            @endif
                        @endforeach
                        <td class="text-center bg2">{{ $count }}</td>
                    </tr>

                </table>
            </div>
        </div>
        <!-- personalName -->

        <div class="row">
            <div class="col-12">
                <div class="">
                    <div class="">
                        @php
                            $i = 1;
                        @endphp

                        <table class="table_te">

                            <tr class="bg1">
                                <th class="text-center">#</th>
                                <th class="text-left" style="width:160px;">Nº do candidato</th>
                                <th class="text-left" style="width:360px;">Nome do candidato</th>
                                <th class="text-center" style="width:270px;">e-mail</th> 
                                <th class="text-center">Assinatura</th> 
                                <th class="text-center" style="width:60px;">{{$disciplines[0]->abb ?? '-'}}</th> 
                                <th class="text-center" style="width:60px;">{{$disciplines[1]->abb ?? '-' }}</th> 
                            </tr>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($model as $item)
                                @if($item->state == 'total')
                                    <tr class="bg2">
                                        <td class="text-center bg2">{{ $i++ }}</td>
                                        <td class="text-left bg2 text-center">
                                            {{ $item->cand_number == null ? 'N/A' : $item->cand_number }}</td>
                                        <td class="text-left bg2">{{ $item->name_completo }}</td>
                                        <td class="text-left bg2">{{ $item->email }}</td>
                                        <td class="text-left bg2"></td>
                                        <td class="text-left bg2 text-center">
                                            {{ isset($notas[$item->id][0][0]->nota) ? $notas[$item->id][0][0]->nota : "" }}
                                        </td>
                                        <td class="text-left bg2 text-center">
                                            {{ isset($notas[$item->id][0][1]->nota) ? $notas[$item->id][0][1]->nota : "" }}
                                        </td>
                                        
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                    @include('Reports::pdf_model.signature')
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</main>

@endsection

<script></script>
