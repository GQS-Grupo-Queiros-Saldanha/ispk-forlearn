@extends('layouts.print')
@section('content')


@php
$logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
$documentoCode_documento = 50;
$doc_name = 'Ficha de Inscrição';
                            
$discipline_code = '';
@endphp

<style>
    body {
        font-family: 'Calibri Light', sans-serif;
    }

    html,
    body {
        padding: 0;
    }

    .table td,
    .table th {
        padding: 0;
        border: 0;
    }

    .form-group,
    .card,
    label {
        display: block !important;
    }

    .form-group {
        margin-bottom: 1px;
        font-weight: normal;
        line-height: unset;
        font-size: 0.75rem;
    }

    .h1-title {

        padding: 0;
        margin-bottom: 0;
        font-size: 2em;
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
        /* border-left: 1px solid #BCBCBC;
            border-right: 1px solid #BCBCBC; */
        /* border-bottom: 1px solid #BCBCBC; */
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
        text-transform: uppercase;
        position: relative;
        /* border-top: 1px solid #000;
            border-bottom: 1px solid #000; */
        margin-bottom: 15px;
        background-color: rgb(240, 240, 240);
        /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}'); */
        background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{ $institution->logotipo }}');
        /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
        background-position: 100%;
        background-repeat: no-repeat;
        background-size: 8%;
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

    input,
    textarea,
    select {
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



    .pl-1 {
        padding-left: 1rem !important;
    }

    table {
        page-break-inside: auto
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto
    }

    thead {
        display: table-header-group
    }

    tfoot {
        display: table-footer-group
    }

    .h1-title {

        padding: 0;
        margin-bottom: 0;
        /* font-size: 14pt; */
        font-size: 1.40rem;
        padding-top: 40px;
        /* background-color:red; */
        /* font-weight: bold; */
        width: 100%;

    }

    .table_te {
        background-color: #F5F3F3;
        ;
        width: 100%;
        text-align: right;
        font-family: calibri light;
        margin-bottom: 6px;
        font-size: 20px;
    }

    .cor_linha {
        background-color: #999;
        color: #000;
    }

    .table_te th {
        border-left: 1px solid #fff;
        border-bottom: 1px solid #fff;
        padding: 4px;
         !important;
        text-align: center;
        font-weight: bold;
    }

    .table_te td {
        border-left: 1px solid #fff;
        background-color: #F9F2F4;
        border-bottom: 1px solid white;
        font-size: 20px;
    }

    .tabble_te thead {}

    .bg0 {
        background-color: #2f5496 !important;
        color: white;
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

    .div-top,
    .div-top table {
        height: 130px;
    }

    .table-main{
        padding-left: 10px;
    }

    .t-color{
            color:#fc8a17;
        }
</style>

<div class="div-top" style="">
    <div class="div-top">


        <table class="table table-main m-0 p-1 bg0 " style="border:none;">
            <tr>

                <td class="td-fotografia " rowspan="12"
                    style="background-image:url('{{ $logotipo }}');width:110px; height:78px; background-size:100%;
                        background-repeat:no-repeat;Background-position:center center;
                        border:none;padding-right:12px!important;top:10px;position:relative;left: 10px;
                        ">
                </td>

            </tr>

            <tr>
                <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>

            </tr>
            <tr>
                <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>
            </tr>





            <tr>
                <td class="bg0" style="border:none; text-align:right;padding-right:10px;">
                    <h1 class="h1-title" style="">
                       <b> {{$doc_name}}</b>
                    </h1>
                </td>
            </tr>
            <tr>
                <td class="data-generate" style="border:none; text-align:right;padding-right:10px;">

                    <span class="" rowspan="1" style="padding-left:20%;position:relative;">
                        Documento gerado a
                        <b>{{ Carbon\Carbon::parse($inscription->created_at)->format('d/m/Y') }}</b>
                    </span>

                </td>
            </tr>

        </table>





        <div class="instituto"
            style="position: absolute; top: 8px; left: 130px; width: 450px; font-family: Impact; padding-top: 40px;color:white;">
            <h4><b>
                @if (isset($institution->nome))
                    @php
                        $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                        $new_name = explode(" ",$institutionName);
                        foreach( $new_name as $key => $value ){
                            if($key==1){
                                echo $value."<br>";
                            }else{
                                echo $value." "; 
                            }
                        } echo "";
                    @endphp
                @else
                    Nome da instituição não encontrado
                @endif 
                </b></h4>
        </div>

    </div>



</div>





{{-- Novo modelo de apresentação --}}






    <main>

        {{-- Não apagar este código --}}


        {{-- <div class="div-top" style="height:80px;">
            <table class="table m-0 p-0">
                    
                <tr>
                    @if ($photo)
                        <td class="td-fotografia" rowspan="12"
                            style="background-image: url('{{ asset('storage/attachment/' . $photo) }}'); width:100px;height:76px; background-position:50%;">
                        </td>
                       
                    @endif
                    <td class="" style=" padding-top:16px;">
                        <h1 class="h1-title">
                           Ficha de Inscrição
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="data_bMatricula" rowspan="4">
                        <style>.data_bMatricula{background-color:transparent;}</style>
                        Documento gerado a
                        <b>{{ Carbon\Carbon::parse($inscription->created_at)->format('d/m/Y') }}</b>
                    </td>
                </tr>
            </table>
        </div> --}}

        <table class="table table-parameter-group">

<thead class="thead-parameter-group">
<th class="th-parameter-group bg1">DADOS PESSOAIS</th>
</thead>

</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
<thead>
    <tr class="bg2">
<th style="font-size: 8pt; border: 0 !important;">
    Nome
</th>

<th style="font-size: 8pt; border: 0 !important;">
    Bilhete de Identidade
</th></tr>
</thead>
<tbody class="">
<tr>
    <td width="25%" style="font-size: 8pt;">
        {{ $personal['name'] }}
    </td>
   
    <td width="50%" style="font-size: 8pt;">
        {{ $personal['bi'] }}
    </td>
</tr>
</tbody>
</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
<thead>
    <tr class="bg2">

   
<th style="font-size: 8pt; border: 0 !important;">
    Telémovel
</th>
<th style="font-size: 8pt; border: 0 !important;">
    Telémovel Alternativo
</th>
<th style="font-size: 8pt; border: 0 !important;">
    Telefone
</th>
<th style="font-size: 8pt; border: 0 !important;"></th>
</tr>
</thead>
<tbody class="">
<tr>
    <td width="25%" style="font-size: 8pt;">
        {{ $personal['mobile_phone'] }}
    </td>
    <td width="25%" style="font-size: 8pt;">
        {{ $personal['mobile_phone_alt'] }}
    </td>
    <td width="25%" style="font-size: 8pt;">
        {{ $personal['phone'] }}
    </td>
    <td width="25%" style="font-size: 8pt;"></td>
</tr>
</tbody>
</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
<thead>
    <tr class="bg2">
<th style="font-size: 8pt; border: 0 !important;">
    Email
</th>
<th style="font-size: 8pt; border: 0 !important;">
    Email Pessoal
</th></tr>
<tbody class="">
<tr>
    <td width="50%" style="font-size: 8pt;">
        {{ $personal['email'] }}
    </td>
    <td width="50%" style="font-size: 8pt;">
        {{ $personal['email_2'] }}
    </td>
</tr>
</tbody>
</table>

<br>

<table class="table table-parameter-group">

<thead class="thead-parameter-group">
<th class="th-parameter-group bg1">DADOS CURRICULARES</th>
</thead>

</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
<thead>
    <tr class="bg2">
<th style="font-size: 8pt; border: 0 !important;">
    Curso
</th>  

<th style="font-size: 8pt; border: 0 !important;">
    Código da Inscrição
</th>
</tr>
<tbody class="">
<tr>
    <td width="25%" style="font-size: 8pt;">
        {{ $inscription->course}}
    </td>
   
      <td width="25%" style="font-size: 8pt;">
         {{ $inscription->code }}
    </td>
   
</tr>
</tbody>
</table>

<br>

<br>
<br>
<br>
<br>
<table class="table-borderless" style="margin-left:4px;">
<thead style="text-align:left;">

</thead>
<tbody>
   

    <tr>

        <td style="font-size: "><b>O estudante</b><br><br>

            __________________________________________________________________<br>
            {{ $personal['name'] }}

            

        </td>

        <td style="color: white;">_____</td>
        

        <td style="font-size: "><b>Staff da IE</b><br><br>

            __________________________________________________________________<br>
            {{ $created_by }}

            

        </td>

    </tr>

</tbody>
</table>
<div>


</div>
<br>
<br>
<br>
<br>
@include('Reports::pdf_model.signature')

<br><br>



    </main>

@endsection
