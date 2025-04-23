@extends('layouts.print')
@section('title', __('Estudantes - Cursos profissionais'))
@section('content')

 
 
    
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'ESTUDANTES - CURSOS PROFISSIONAIS';
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
                               
                                <th class="text-center">Curso</th>
                                <th class="text-center">Edição</th>
                                <th class="text-center">Total de inscritos</th>
                            </tr>
                            <tr class="bg2">
                                <td class="text-center bg2">
                                  {{ $lectiveYears->currentTranslation->display_name}}
                            </td>

                            <td class="text-center bg2">{{ $edition->course }}</td>

                            <td class="text-center bg2">
                                        {{  $edition->number }}
                            </td>

                        


                       
                        <td class="text-center bg2">{{ $model->count() }}</td>
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
                                <th class="text-left" style="width:160px;">Nº de inscrição</th>
                                <th class="text-left" style="width:360px;">Nome</th>
                                <th class="text-center" style="width:270px;">E-mail</th> 
                                <th class="text-center">Assinatura</th> 
                                <th class="text-center" style="width:60px;">Nota</th> 
                               
                            </tr>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($model as $item)
                             
                                    <tr class="bg2">
                                        <td class="text-center bg2">{{ $i++ }}</td>
                                        <td class="text-left bg2 text-center">
                                            {{ $item->code == null ? 'N/A' : $item->code }}</td>
                                        <td class="text-left bg2">{{ $item->nome }}</td>
                                        <td class="text-left bg2">{{ $item->email }}</td>
                                        <td class="text-left bg2"></td>
                                        <td class="text-left bg2 text-center" width="120">
                                           
                                        </td>
                                        
                                        
                                    </tr>
                             
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
