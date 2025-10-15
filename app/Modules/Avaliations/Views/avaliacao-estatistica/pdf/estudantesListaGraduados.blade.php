@extends('layouts.print')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <main>
        @php
            $doc_name = 'Relatório de graduados';
            $discipline_code = '';
        @endphp
        @include('Reports::pdf_model.forLEARN_header')
         
        <div class="">
        <div class="">
            <div class="row">
                <div class="col-12 mb-4">
                  
                     
                            
                </div>
            </div>
            <!-- personalName -->
            <br>
            <br>
            <div class="row">
                <div class="col-12" style="margin-top:4px;">
                    <div class="">
                        <div class="">

                            <table class="table_te" >
                     
                            <tr class="bg1">
                                <th class="text-center bg2" style="font-size: 12pt; padding: 0px; ">#</th>
                                <th class="text-center bg2" style="font-size: 12pt; padding: 0px;">Matrícula</th>
                                <th class="text-center bg2" style="font-size: 12pt; padding: 0px;">Nome do(a) estudante</th>
                                <th class="text-center bg2" style="font-size: 12pt; padding: 0px; ">Sexo</th>
                                <th class="text-center bg2" style="font-size: 12pt; padding: 0px; ">Nota</th>
                            </tr>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($estudantes as $item)
                            <tr>    
                                <td class="text-center"style="width:40px;">{{$i++}}</td> 
                                <td class="text-center" style="">{{$item->matricula}}</td> 
                                <td class="text-left" style="">{{$item->nome_completo}}</td> 
                                <td class="text-left" style="">{{$item->sexo}}</td>  
                                <td class="text-left" style="">{{$item->nota}}</td> 
                            </tr>
                            @endforeach 

                            </table>   
                            </div>
                            <br>
                            <br>
                                                       

                            <div class="">
                           
                            
                            </div>
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