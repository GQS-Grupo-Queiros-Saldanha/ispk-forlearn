@extends('layouts.print')
@section('title', __('Pauta de Curso Profissional'))
@section('content')

 
 
    
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'PAUTA DE CURSO PROFISSIONAL';
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

                        <thead>
                            <tr class="bg1">
                                <th class="text-center">Ano lectivo</th>
                               
                                <th class="text-center">Curso</th>
                                <th class="text-center">Edição</th>
                          
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="bg2">
                                <td class="text-center bg2">
                                  {{ $lectiveYears->currentTranslation->display_name}}
                            </td>

                            <td class="text-center bg2">{{ $edition->course }}</td>

                            <td class="text-center bg2">
                                        {{  $edition->number }}
                            </td>

                 
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- personalName -->

        <div class="row">
            <div class="col-12">
                <div class="">
                    <div class="">
                    <table class="table_te">
                <thead>
                    <tr class="bg1">
                        <th class="text-center">#</th>
                        <th class="text-center">Nº de inscrição</th>
                        <th class="text-center">Nome</th>
                        <th class="text-center">Nota</th>
                    </tr>
                </thead>
                <tbody class="bg2">
                    @php $i = 0; @endphp
                    @foreach($content as $item)
                    <tr class="bg1">
                    <td class="text-center bg2">{{ $i++  }}</td>
                    <td class="text-center bg2">{{ $item->code }} </td>         
                    <td class="text-center bg2"> {{ $item->student_name }}</td> 
                    <td class="text-center bg2"> {{ $item->grade }} </td> 
                    </tr>
                    @endforeach
                </tbody>
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

