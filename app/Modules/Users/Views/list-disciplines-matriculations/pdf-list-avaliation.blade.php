@switch($type)
@case('segunda_chamada')<title>{{$metric}} (2ª chamada)</title>@break
@case('recurso')<title>Lista do Exame de Recurso</title>@break
@case('exame_especial')<title>Lista do Exame Especial</title>@break
@case('melhoria_nota')<title>Lista do Exame de Melhoria de Nota</title>@break
@case('exame_extraordinario')<title>Lista do Exame Extraordinário</title>@break
@endswitch
@extends('layouts.print')
@section('content')
@php
$logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
switch($type){
case 'segunda_chamada': $doc_name = $metric .' (2ª chamada)';break;
case 'recurso': $doc_name = 'Lista do Exame de Recurso';break;
case 'exame_especial': $doc_name = 'Lista do Exame Especial';break;
case 'melhoria_nota': $doc_name = 'Lista do Exame de Melhoria de Nota';break;
case 'exame_extraordinario': $doc_name = 'Lista do Exame Extraordinário';break;
}

$documentoCode_documento = 50;

$discipline_code = '';
@endphp
<main>
    @include('Reports::pdf_model.forLEARN_header')
    <!-- aqui termina o cabeçalho do pdf -->
    <div class="">
        <div class="">
            <div class="row">
                <div class="col-12 mb-4">
                    <table class="table_te">


                        <tr class="bg1">
                            <th class="text-center">Curso</th>
                            <th class="text-center">Ano</th>
                            <th class="text-center">Ano lectivo</th>
                            <th class="text-center">Disciplina</th>
                            @if(isset($turmaC)) <th class="text-center">Turma</th>
                            <th class="text-center">Regime</th> @endif
                            <th class="text-center">Nº de inscritos</th>
                        </tr>
                        <tr class="bg2">
                            <td class="text-center bg2">{{$curso}}</td>
                            <td class="text-center bg2">{{$ano}}º</td>
                            <td class="text-center bg2">
                                @foreach ($lectiveYears as $anoLectivo)
                                {{$anoLectivo->currentTranslation->display_name}}
                                @break
                                @endforeach
                            </td>
                            <td class="text-center bg2">{{$nome_disciplina}}</td>

                            @if(isset($turmaC)) <td class="text-center bg2">{{$turmaC}}</td>

                            <td class="text-center bg2">{{$regime==0?"Frequência":"Exame"}}</td> @endif

                            @php
                            $count=0;
                            @endphp

                            @foreach ($model as $curso)
                            @php
                            $count++;
                            @endphp
                            @endforeach
                            <td class="text-center bg2">{{$count}}</td>
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
                            $i=1;
                            @endphp

                            <table class="table_te">

                                <tr class="bg1">
                                    <th class="text-center" style="font-size: 14pt; padding: 0px; ">#</th>
                                    <th class="text-center" style="font-size: 14pt; padding: 0px;">Matrícula</th>
                                    <th class="text-center" style="font-size: 14pt; padding: 0px; width:100px;">Nome do(a) estudante</th>
                                    <th class="text-center" style="width:210px">e-mail</th>
                                    <th class="text-center">Assinatura</th>
                                    <th class="text-center">Nota</th>


                                </tr>
                                @php
                                $i=1;
                                @endphp

                                @foreach ($model as $item)

                                @if(isset($item->email))
                                <tr class="bg2">
                                    <td class="text-center bg2" style="font-size: 14pt;">{{$i++}}</td>
                                    <td class="text-center bg2" style="width:150px;font-size: 14pt;">{{$item->matricula}}</td>
                                    <td class="text-left bg2" style="width:390px;font-size: 14pt;">{{$item->student}}</td>
                                    <td class="text-left bg2" style="width:270px;font-size: 14pt;">{{$item->email}}</td>
                                    <td class="text-left bg2" style="font-size: 14pt;width:400px;"></td>
                                    <td class="text-left bg2" style="width:70px;font-size: 14pt;"></td>

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

<script>
</script>