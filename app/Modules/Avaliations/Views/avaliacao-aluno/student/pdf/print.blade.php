@section('title', "Notas das avaliações")
@extends('layouts.printForSchedule')
@section('content')

<style>
    .div-top {
          text-transform: uppercase;
            position: relative;
            /* border-top: 1px solid #000;
            border-bottom: 1px solid #000; */
            margin-bottom: 5px;
            background-color: rgb(240, 240, 240);
            
               background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}'); 
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 65%;

    }

    /* DivTable.com */
    .divTable {
        display: table;
        width: 100%;
    }

    .divTableRow {
        display: table-row;
    }

    .divTableHeading {
        background-color: #EEE;
        display: table-header-group;
    }

    .divTableCell,
    .divTableHead {

        display: table-cell;
        padding: 3px 10px;
    }

    .divTableHeading {
        background-color: #EEE;
        display: table-header-group;
        /* font-weight: bold; */
    }

    .divTableFoot {
        background-color: #EEE;
        display: table-footer-group;
        /* font-weight: bold; */
    }

    .divTableBody {
        display: table-row-group;
    }

    .pl-1 {
        padding-left: 1rem !important;
        padding-top: 10px;
    }

    .h1-title {
        padding: 0;
        margin-bottom: 0;
        font-size: 25pt;
        padding-top: 10px;
    }

    .td-institution-name {
        vertical-align: middle !important;
        font-weight: bold;
        text-align: right;
        float: right;
        padding-top: 30px;
    }

    .td-institution-logo {
        vertical-align: middle !important;
        text-align: center;

    }

    .img-institution-logo {
        width: 50px;
        height: 50px;
        float: right;
        padding-top: 20px;
        height: 100px;
        width: 100px;
    }

    .item1 {
        background-color: red;
    }

    .h1-name {
        padding: 0;
        margin-bottom: 0;
        font-size: 20pt;
        padding-top: 15px;
        text-align: center;
    }

    .h1-tex-name-div {
        text-align: center;
        align-content: center;
    }

    .itens {
        font-size: 12pt;
        color: #000;
        /* font-weight: bold; */
    }

    .table-parameter-group {
        page-break-inside: avoid;

    }

    table,
    tr,
    td,
    th,
    tbody,
    thead,
    tfoot {
        page-break-inside: avoid !important;
    }

    .table-parameter-group td,
    .table-parameter-group th {
        vertical-align: unset;
    }

    .tbody-parameter-group {
        border-top: 0;
        border-left: 1px solid #fff;
        border-right: 1px solid #fff;
        border-bottom: 1px solid #fff;
    }

    .thead-parameter-group {
        color: white;
        background-color: #6C7AE0;
        text-align: center;
    }

    .th-parameter-group {
        padding: 7px 6px !important;
        font-size: 10.5pt !important;
        font-family: Arial, Helvetica, sans-serif;

    }

    .td-parameter-column {
        padding-left: 5px 5px !important;
        border-left: 1px solid #fff;
        border-right: 1px solid #fff;

    }

    .tfoot {
        /*border-right: 1px solid #BCBCBC !important;
            border-left: 1px solid #BCBCBC !important;*/
        border-bottom: 1px solid #fff !important;
        text-align: right;
    }

    .td-of-th {
        text-align: center;
        background-color: #EEE;
    }

</style>
<main>
    <div class="div-top" style="height:135px; height:80px;">
        <table class="table m-0 p-0">
            <tr>
                {{-- <td class="td-fotografia" rowspan="3" style="background-image: ; width:100px; height:130px;"> --}}
                </td>
                <td class=""  style=" padding-top:16px;">
                    <h1 class="h1-title" >
                        Notas das avaliações
                    </h1>
                </td>
           {{-- <td class="td-institution-name" rowspan="2">
                    Instituto Superior<br>Politécnico Maravilha
                </td>
                <td class="td-institution-logo" rowspan="2">
                    <img class="img-institution-logo" src="{{ asset('img/logo.jpg') }}" alt="">
                </td>--}}
            </tr>
                <tr>
                    <td class="" style="padding-bottom: 20px;">
                        <b>Data: {{ date('d/m/Y') }} </b>
                    </td>
                </tr>
        </table>
    </div>
    @include('Avaliations::avaliacao-aluno.student.pdf.partials')
 
</main>
@endsection
{{--<script>
        window.print();
    </script>--}}

