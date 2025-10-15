@extends('layouts.printForSchedule')
@section('content')


@php $now = \Carbon\Carbon::now(); @endphp
<title>Conta corrente</title>
<main>
       
    @php
    $logotipo = "https://".$_SERVER['HTTP_HOST']."/instituicao-arquivo/".$institution->logotipo;
    $doc_name = "CONTA CORRENTE";
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
    
    /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
    background-position: 100%;
    background-repeat: no-repeat;
    background-size: 63%;
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

.tdata {
    width: 350px !important;
}

.tdata tr th {

    font-size: 20px;
}
.bg0,.div-top{
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
td,th{
    font-size: 12px!important;
}
.h1-title,.data-generate{
    text-align:right;
    /* padding-right: 10px; */
    padding-top: 20px;
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
        <td class="bg0" style="border:none;padding-right:3px;">
            <h1 class="h1-title" style="">
               <b> {{$doc_name}}</b>
            </h1>
        </td>
    </tr>
    <tr>
        <td class="data-generate" style="border:none;padding-right:3px;">

            <span class="" rowspan="1" style="">
                Documento gerado a
                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
            </span>

        </td>
    </tr>

</table>





<div class="instituto"
    style="position: absolute; top: 8px; left: 130px; width: 440px; font-family: Impact; padding-top: 20px;color:white;">
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
                } 
            @endphp
        @else
            Nome da instituição não encontrado
        @endif 
        </b></h4>
</div>

</div>



</div>

    @section('content')

    {{-- Main content --}}
    <style>
        
    </style>
    <div class="content-fluid">
        <div class="">
            <div class="row">
                <div class="col">
                    <div class="card" >
                        <div class="tabela_div" >
                            <table class="table table-parameter-group" >
                                <thead class="thead-parameter-group ">
                                    <tr class="bg1">
                                        <th style="font-size: 1pc" class="th-parameter-group" colspan="2">Estudante</th>
                                        <th style="font-size: 1pc" class="th-parameter-group" colspan="2">e-mail</th>
                                        <th style="font-size: 1pc" class="th-parameter-group" colspan="1">Matricula</th>
                                        <th style="font-size: 1pc" class="th-parameter-group">Curso</th>
                                        @if (!$lectiveYears->isEmpty())
                                            <th style="font-size: 1pc" class="th-parameter-group">Ano lectivo</th>
                                        @endif


                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td  style="font-size: 1pc; padding-left: 5px; padding-bottom: 5px; padding-top: 5px"colspan="2">{{$personal['name']}}</td>

                                        <td  style="font-size: 1pc; padding-left: 5px; padding-bottom: 5px; padding-top: 5px"colspan="2" >{{$getUserInfo->email}}</td>

                                        <td  style="font-size: 1pc; padding-left: 5px; padding-bottom: 5px; padding-top: 5px"colspan="1"> {{$personal['n_mecanografico']}}</td>

                                        @foreach ($user_requests as $request)
                                        @if ($loop->first)
                                        @foreach ($user->courses as $user_course)
                                        <td style="font-size: 1pc; padding-left: 5px; padding-bottom: 5px; padding-top: 5px">{{$user_course->currentTranslation->display_name}}</td>
                                        @endforeach
                                        @endif
                                        @endforeach
                                        @if (!$lectiveYears->isEmpty())
                                            <td  style="font-size: 1pc; padding-left: 5px; padding-bottom: 5px; padding-top: 5px" class="text-center">{{$lectiveYears[0]->currentTranslation->display_name}}</td>
                                        @endif

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                #requests-trans-table thead tr{
                    background-color: #8eaadb !important;
                    padding-top: 4px;
                    padding-bottom: 4px;
                }
            </style>
            <?php
            $contaCorrente = storage_path('app/public/contaCorrente/contaCorrente.blade.php');
            include $contaCorrente;
            ?>

           

            <style>
                .div-borda,.accoes-tesoraria,.checkbox-tesoraria{
                    display: none;
                }
                .table-tesoraria{
                    margin-top: 1.5pc;
                    width: 100%;
                    font-size: 2pc;
                    
                }
                .divtable{
                    margin-top: 10px;
                }
                .table-tesoraria td {
                    font-size: 0.8pc;
                    margin-bottom: 5px;
                    margin-top: 5px;
                    text-align: left;
                    padding-bottom: 7px;
                    padding-top: 7px;
                    padding-left: 10px;
                }
                .table-tesoraria th{
                    font-size: 1pc;
                    text-align: left;
                    padding-left: 10px;

                }
            </style>



        </div>
    </div>


@endsection
@section('scripts')
@parent
    <script>
        $(function () {
            let user_requests = {!!$user_requests!!};
            console.log(user_requests);

            let balance = {!!$balance!!};
            let personal = {!!$user ->credit_balance!!};

            let getDisciplines = {!! $getDisciplines !!};

            console.log(balance);
            console.log(personal);
            console.log(getDisciplines);
        });
    </script>
@endsection