@extends('layouts.print')
@include('Reports::pdf_model.pdf_header')
<br>
<style>
    #tabela-aprovados {
        margin-bottom: 10px;
        width: 100%;
    }

    #tabela-total {

        width: 22%;
    }

    #tabela-reprovados {
        width: 70%;
    }

    #tabela-aprovados th,
    #tabela-aprovados td,
    #tabela-aprovados {
        text-align: center !important;
        /* background-color: #adadad; */

    }

    #tabela-reprovados th,
    #tabela-reprovados td,
    #tabela-reprovados {
        text-align: center !important;
        /* background-color: #c1c1c1; */

    }

    #tabela-total th,
    #tabela-total td,
    #tabela-total {
        text-align: center !important;
        /* background-color: #a09e9e; */

    }


    #tabela-reprovados {
        float: left;
        margin-right: 8%;
        padding: 2px;

    }


    .titulo {
        text-transform: uppercase;
        /* background-color: #adadad; */
        width: 250px;
        text-indent: 10px;
        padding: 4px;

    }

    #opaco {
        background-color: transparent;
    }

    .tabela-estatistica thead {
        /* display: table-row-group; */
    }

    .tabela-estatistica td {
        text-align: center;
        white-space: pre-wrap;
    }


    #tabela-aprovados th,
    #tabela-reprovados th,
    #tabela-total th,
    #tabela-aprovados td,
    #tabela-reprovados td,
    #tabela-total td {
        padding: 2px 4px 1px 4px;

        border: 1px solid white;
        font-size: 10px;
    }

    #tabela-aprovados thead,
    #tabela-reprovados thead,
    #tabela-total thead {
        width: 100%;
    }

    #tabela-aprovados td,
    #tabela-reprovados td,
    #tabela-total td {
        font-size: 10px;
        border: 1px solid white;
    }

    .titulo-aprovados {
        background-color: #a8d08d !important;
    }

    .titulo-reprovados {
        background-color: #c55a11 !important;
    }

    .titulo-avaliados {
        background-color: #8eaadb !important;

    }

    .escala-135 {
        background-color: #ffe598 !important;
    }

    .escala-24 {
        background-color: #f7caac !important;
    }

    .anodisc {
        background-color: #b4c6e7;
    }

    .t {
        background-color: #aeabab;
    }

    .t-descricao {
        background-color: #ffd965;
    }
</style>
<br>
<br>
@section('content')
    <style>
        .tabela-estatistica thead {
            display: table-row-group;
        }

        .tabela-estatistica td {
            text-align: center;
            white-space: pre-wrap;
        }

        th,
        td {
            padding: 2px 4px 1px 4px;

            border: 1px solid white;
            font-size: 10px;
        }

        thead {
            width: 100%;
        }

        thead tr th,
        tbody tr th {
            font-size: 12pt !important;
        }

        thead tr th {
            font-weight: 500 !important;
        }

        tbody tr th {
            font-weight: 400 !important;
        }

        td {
            font-size: 10px;
            border: 1px solid white;
        }

        #tabela-aprovados {
            float: left;
        }

        #tabela-reprovados {
            float: left;
            margin-right: 34px
        }

        #tabela-total {}

        .bnone {
            border: none !important;
        }


        h4 {
            text-transform: uppercase;
        }

        .titulo-aprovados {
            background-color: #a8d08d;
        }

        .titulo-reprovados {
            background-color: #ffc7a1;
        }

        .titulo-avaliados {
            background-color: #8eaadb;

        }

        .escala-135 {
            background-color: #ffe598;
        }

        .escala-24 {
            background-color: #f7caac;
        }

        .escala-22 {
            background-color: #8eaadb;
        }

        .anodisc {
            background-color: #d6bd8f;
        }

        .t {
            background-color: #aeabab;
            font-weight: 700 !important;
        }

        .t-descricao {
            background-color: #ffd965;
        }

        .tr-linha th {
            font-weight: 900 !important;
        }

   
    </style>

    <br>
    <br>
    <main>
        <table class="tabela-estatistica" cellspacing="0" style="width: 80% !important;margin-left: 10%!important;">
            <thead style="">
                <tr>
                    <th class="bnone" style="text-align:left;font-size: 40px!|important" colspan="50">

                        Curso: Licenciatura em

                        {{ $curso }}

                    </th>
                </tr>

                <tr>
                  
                    
                    <th colspan="5" class="bnone escala-24">BACHAREL</th>
                    <th colspan="5" class="bnone escala-22">LICENCIADO</th>
                    <th colspan="5" class="bnone escala-24">MESTRE</th>
                    <th colspan="5" class="bnone escala-22">DOUTOR</th>
                    <th colspan="" class="bnone"></th>
                    <th colspan="5" class="bnone t">TOTAL</th>
                </tr>
                <tr class="tr-linha">
                    
                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th colspan="" class="bnone"></th>
                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                </tr>
            </thead>


            <tbody>

                
                <tr>
                  
                    
                    {{-- BACHAREL --}}

                    <th class="escala-22">{{$docentes[$id_curso]["bacharel_M"]}}</th>
                    <th class="escala-22">{{$docentes[$id_curso]["bacharel_MP"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["bacharel_F"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["bacharel_FP"]}}</th>
                    <th class="t">{{$docentes[$id_curso]["bacharel_T"]}}</th>
                    
                    {{-- LICENCIADO --}}

                    <th class="escala-22">{{$docentes[$id_curso]["licenciado_M"]}}</th>
                    <th class="escala-22">{{$docentes[$id_curso]["licenciado_MP"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["licenciado_F"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["licenciado_FP"]}}</th>
                    <th class="t">{{$docentes[$id_curso]["licenciado_T"]}}</th>
                    
                    {{-- MESTRE --}}

                    <th class="escala-22">{{$docentes[$id_curso]["mestre_M"]}}</th>
                    <th class="escala-22">{{$docentes[$id_curso]["mestre_MP"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["mestre_F"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["mestre_FP"]}}</th>
                    <th class="t">{{$docentes[$id_curso]["mestre_T"]}}</th>
                    
                    {{-- DOUTOR --}}

                    <th class="escala-22">{{$docentes[$id_curso]["doutor_M"]}}</th>
                    <th class="escala-22">{{$docentes[$id_curso]["doutor_MP"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["doutor_F"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["doutor_FP"]}}</th>
                    <th class="t">{{$docentes[$id_curso]["doutor_T"]}}</th>
                    <th colspan="" class="bnone"></th>
                    
                    {{-- TOTAL  --}}

                    <th class="escala-22">{{$docentes[$id_curso]["TM"]}}</th>
                    <th class="escala-22">{{$docentes[$id_curso]["TMP"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["TF"]}}</th>
                    <th class="escala-24">{{$docentes[$id_curso]["TFP"]}}</th>
                    <th class="t">{{$docentes[$id_curso]["T"]}}</th>
                    
                </tr>
               
            </tbody>
        </table>


        <br>
    </main>
@endsection
