@extends('layouts.print')
@section('content')

 <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
                
    <style>
     
       @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

      
</style>
    <main>
      
        @php
            $doc_name = 'PAUTA DE '.$discipline_name;
            $discipline_code = '';
        @endphp
        @include('Reports::pdf_model.forLEARN_header')

        <style>
            tr,td{
                font-size: 20px!important;
            }
        </style>
        
        @php $x = 0; $i = 1; @endphp
        <br>  
        <table class="table_te" style="width: 100%;font-size:14px;">
            
            <thead id="head" style="background-color:#F5F3F3; border:white;">
                <tr class="bg1">
                    <th>#</th>
                    <th>Matrícula</th>
                     <th>Nome</th>
                    <th> Nota </th>
                </tr>
            </thead>

            <tbody id="">
                <?php $i=1;?>
                @foreach ($avaliacaos_student as $item)                    
                        <tr class="bg2">
                            <td class="bg2" style="text-align:center;width:50px;;">{{$i++}}</td>
                            <td class="bg2" style="text-align:center;width:150px;">{{$item->code_aluno}}</td>
                            <td class="bg2" style="text-align:left;width:400px;">{{$item->nome_aluno}}</td>
                            <td class="bg2" style="text-align:center;width:100px;">{{$item->nota_aluno!=null?$item->nota_aluno:"F"}}</td>    
                        </tr>
               @endforeach
            </tbody>
        </table>  




        
        {{-- termina aqui --}}
    
            <div class="col-12">
            <br>
            <br>
            <table class="table-borderless">
                <thead style="text-align:left:">
                    <tr>
                    <th colspan="2" style="font-size: 9pt;"></th> </tr>
                 
                </thead>
                <tbody>                   
                    <tr>
                         <td style="font-size: 10pt; font-weight:bold;  padding-bottom:17px; "><b></b>Assinaturas</b></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                                    
                        <td style="font-size: 10pt; ">Docente:<br><br> __________________________________   </td>


                        <td style="font-size: 10pt; ; color: white;">_____________________</td>


                        <td style="font-size: 10pt; ">Pelo gabinete de termos: <br><br>__________________________________</td>
                    </tr>
                        
 
              
                </tbody>
            </table> 
        </div>
        {{-- @include('Avaliations::avaliacao-aluno.reports.pdf_partials') --}}
    </main>

@endsection

<script>
    // window.print();
</script>
