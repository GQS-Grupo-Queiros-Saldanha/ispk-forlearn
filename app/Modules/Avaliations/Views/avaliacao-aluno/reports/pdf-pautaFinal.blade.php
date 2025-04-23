@extends('layouts.print')
@section('content')

 <link href="http://fonts.cdnfonts.com/css/calibri-light" rel="stylesheet">
                
    <style>     
       @import url('http://fonts.cdnfonts.com/css/calibri-light');
                
        body{
         font-family: 'Calibri Light', sans-serif;                                         
        }
    .form-group {
        /* margin-bottom: 1px; */
        font-weight: normal;
        line-height: unset;
        font-size: 0.75rem;
    }
    
    .h1-title {
        padding: 0;
        margin-bottom: 0;
        /* font-size: 14pt; */
        font-size: 1.67rem;
        padding-top:10px;
        /* background-color:red; */
        font-weight: bold;
        width: 60%;
        padding-left: 2px;
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


    .thead-parameter-group {
        color: white;
        background-color: #3D3C3C;
    }

    .th-parameter-group {
        padding: 2px 5px !important;
        font-size: .625rem;
    }
    .div-top {
        position: relative;
        margin-bottom: 5px;
        background-color: rgb(240, 240, 240);
        background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}'); 
        /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
        background-position: 100%;
        background-repeat: no-repeat;
        background-size: 75%;
    }

    input, textarea, select {
        display: none;
    }

    .td-fotografia {
        background-size: cover;
        padding-left: 10px !important;
        padding-right: 10px !important;
        width: 70px;
        height: 100%;
        margin-bottom: 5px;
    }

    .pl-1 {
        padding-left: 1rem !important;
    }
           table     { page-break-inside:auto }
               tr    { page-break-inside:avoid; page-break-after:auto }
               thead { display:table-header-group }
               tfoot { display:table-footer-group }
</style>
    <main>
        <div class="div-top" style="height:110px;" >
            
            <table  class="table m-0 p-0 " style="border:none">
                <tr>
                    <td class="" style="border:none;">
                        <h1 class="h1-title">
                            Pauta de Final
                        </h1>
                    </td>
                </tr>

                <tr>                
                </tr>
            </table>
        </div>
        
        <table class="table_te">
            <style>
               .table_te{background-color: #F5F3F3; !important ;width:100%;text-align:left;font-family:calibri light; margin-bottom: 6px;}
               .table_te th{ border-left:1px solid #fff;border-bottom: 1px solid #fff;padding: 4px; !important; text-align:center;}
               .table_te td{border-left:1px solid #fff;background-color:#F9F2F4; } 
               .tabble_te thead{}
            </style>
            
            <thead style="border:none;">
                <th >Curso</th>
                <th >Disciplina</th>
                <th >Ano</th>
                <th >Ano lectivo</th>
                <th >Regime</th>
                <th >Turma</th>
                <th >Turno</th>
            </thead>
            
            <tbody> 
                <!---CABEÇALHO ---> 
             
            </tbody>
        </table>

        @php $x = 0; $i = 1; @endphp
        <br>
        <table class=" table_corpo">
            <style>
                table{font-family:calibri light; width: 100%;}
                .table_corpo th{ border-left:1px solid #fff;border-bottom: 1px solid #fff;padding: 4px; !important; text-align:left;}
                .disciplina_class_order { background-color:#F5F3F3;color:#444; }
                .table_corpo tbody td{padding: 5px; !important; background-color:#F9F2F4; border:1px solid #fff;}     
            </style>
            <?php $example = ["PF2", "PF1", "AO"];
                sort($example);?>                    
            
            <thead id="head" style="background-color:#F5F3F3; border:white;">
                <th >#</th>
                <th >Matrícula</th>
                <th >Nome</th>
                @for ($a =0; $a < count($example); $a++)                     
                    <th>{{$example[$a]}}</th>
                @endfor
                <th> MAC </th>
                {{-- <th> Observações </th> --}}
            </thead>

            <tbody id="">
                <!--- LISTA DE ALUNOS --->
                <?php $i=1;?>              
                @foreach ($avaliacaos_student as $item)                                      
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$item->code_aluno}}</td>
                        <td>{{$item->nome_aluno}}</td>
                        <!--- PF1 --->
                        @if ($item->nota_aluno >= 10)
                            <td >{{$item->nota_aluno}}</td>    
                        @else
                            <td style="color: red">{{$item->nota_aluno}}</td>    
                        @endif
                        <!--- PF2 --->
                        @if ($item->nota_aluno >= 10)
                            <td >{{$item->nota_aluno}}</td>    
                        @else
                            <td style="color: red">{{$item->nota_aluno}}</td>    
                        @endif
                        <!--- AO --->
                        @if ($item->nota_aluno >= 10)
                            <td >{{$item->nota_aluno}}</td>    
                        @else
                            <td style="color: red">{{$item->nota_aluno}}</td>    
                        @endif
                        <!--- MAC --->
                        @if ($item->nota_aluno >= 10)
                            <td >{{($item->nota_aluno + $item->nota_aluno + $item->nota_aluno) / 3}}</td>    
                        @else
                            <td style="color: red">{{($item->nota_aluno + $item->nota_aluno + $item->nota_aluno) / 3}}</td>    
                        @endif
                    </tr>
               @endforeach
            </tbody>
        </table> 
        
        {{-- termina aqui --}}
    
            <div class="col-12">
            </br>
            </br>
            <table class="table-borderless">
                <thead style="text-align:left:">
                    <th colspan="2" style="font-size: 9pt;">
                        
                    </th> 
                 
                </thead>
                <tbody>                   
                    <tr>
                         <td style="font-size: 10pt; font-weight:bold;  padding-bottom:17px; "><b></b>Assinaturas</b></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                                            <tr>
                        <td style="font-size: 10pt; ">Docente:<br><br> ________________________________________________________________________   </td>


                        <td style="font-size: 10pt; ; color: white;">_____________________


                        <td style="font-size: 10pt; ">Pelo gabinete de termos: <br><br>____________________________________________________________________</td>
                    </tr>
                        
                        <!--<td style="font-size: 9pt;">Docente: ________________________________________________________________________     </td>-->
                        <!--<td style="font-size: 9pt;">Pelo gabinete de termos: ____________________________________________________________________</td>-->
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
