@extends('layouts.printForSchedule')
<style>
    html, body {

       }

       body {
           font-family: Montserrat, sans-serif;
       }

       .table td,
       .table th {
           padding: 0;
           border: 0;
       }

       .form-group, .card, label {
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
       }

       .img-institution-logo {
           width: 73px;
           height: 73px;
           margin: 0px;
           padding: 0px;
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
           border-left: 1px solid #BCBCBC;
           border-right: 1px solid #BCBCBC;
           border-bottom: 1px solid #BCBCBC;
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
           text-transform: uppercase;
           position: relative;
           border-top: 1px solid #000;
           border-bottom: 1px solid #000;
           margin-bottom: 25px;
           padding-top: 1px;
           padding-bottom: 1px
       }

       .td-institution-name {
           vertical-align: middle !important;
           font-weight: bold;
           text-align: right;
           width: 20pc;
           text-indent: 1.5em
       }

       .td-institution-logo {
           vertical-align: middle !important;
           text-align: center; 
           padding: 0px;
           margin: 0px
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

       input, textarea, select {
           display: none;
       }

       .pl-1 {
           padding-left: 1rem !important;
       }

       .header-user {
           padding: 0 !important;
           text-align: left !important;
       }
       #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        
        #customers .td, #customers th {
            border: 1px solid #ddd;
            padding: 5px;
        }
        
        #customers .tr:nth-child(even){background-color: #f2f2f2;}
        
        #customers .tr:hover {background-color: #ddd;}
        
        #customers th {
            padding-top: 6px;
            padding-bottom: 6px;
            text-align: left;
            background-color: #33302f;
            color: white;
        }

        







        #customer {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        
         #customer th {
            border: 1px solid #ddd;
            padding: 5px;
        }
        
          
        #customer .td{
            border:none;
            padding: 5px;
        }
        #customer .tr:nth-child(even){background-color: #f2f2f2;}
        
        #customer .tr:hover {background-color: #ddd;}
        
        #customer th {
            padding-top: 6px;
            padding-bottom: 6px;
            text-align: left;
            background-color:#403e3e;
            color: white;
        }
</style>
@php $now = \Carbon\Carbon::now(); @endphp
<main>
     <div class="div-top">
            <table class="table m-0 p-0">
                <tr>
                    <td class="pl-1">
                        <h1 style="padding: 0" class="h1-title" style="font-size: 24px">
                            Estado da matrícula
                        </h1>
                        <small style="font-size: 0.8pc">Histórico de estados do estudante</small>
                        <hr style="padding: 0">
                    </td>
                    <td class="td-institution-name" rowspan="2" style="font-size: 18px; background: #eff5f7">
                        {{$institution->nome}}
                    </td>
                    <td class="td-institution-logo" rowspan="2" style="background: #eff5f7">
                        <img class="img-institution-logo" src='http://{{ $_SERVER['HTTP_HOST'] }}/instituicao-arquivo/{{ $institution->logotipo }}' alt="">
                    </td>
                </tr>
                <tr>
                    <td class="pl-1" style="font-size: 12px">
                        Documento gerado a
                        <b>{{ $now->format('d/m/Y')}}</b>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <h4 id="titulo" style="background-color: #33302f; width: 15pc; color: white; padding-top: 5px; padding-bottom: 3px; padding-left: 5px; padding-left: 5px">Dados do estudante</h4>
        </div>

        
        <div>
            <table  id="customer">
                <tr>
                    <th>Estudante</th>
                    <th>Matrícula</th>
                    <th>E-mail</th>
                    <th>Curso</th>
                </tr>
                <tbody id="lista-data-day">
                    <tr class="tr">
                        <td class="td">{{$getStudent->full_name}}</td>
                        <td class="td" style="text-align: center">{{$getStudent->matricula}}</td>
                        <td class="td" style="text-align: center">{{$getStudent->email}}</td>
                        <td class="td" style="text-align: center">{{$getStudent->curso}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width: 100%;margin-top: 1pc;border-top: rgb(185, 185, 185) 1px solid;padding-top: 8px">
        </div>
        <br><br><br>


        <div>
            <h6>Históricos de estados</h6>
        </div>
        <div>
            <table id="customers">
                <tr>
                    <th>#</th>
                    <th>Sigla</th>
                    <th>Estado</th>
                    <th>Tipo de estado</th>
                    <th>Data</th>
                </tr>
                <tbody id="lista-data-day">
                    @php
                        $i=0;
                    @endphp
                @foreach ($statesHistoric as $item)
                        @php $i++; $data_creadted=$item->occurred_at @endphp
                    <tr class="tr">
                        <td class="td">{{$i}}</td>
                        <td class="td">{{$item->initials}}</td>
                        <td class="td">{{$item->studant_state}}</td>
                        <td class="td" style="text-align: center">{{$item->state_type}}</td>
                        <td class="td" style="text-align: center">{{$data_creadted}}</td>
                    </tr>
                @endforeach
                
                </tbody>
            </table>
        </div>
        {{-- <div style="width: 100%;margin-top: 3pc;border-top: rgb(185, 185, 185) 1px solid;padding-top: 8px">
            <table style="width: 100%">
                <thead>
                    <tr><td style="padding: 0px 0px 10px 10px"><i class="fa-regular fa-clock"></i> Hora trabalhada no mês [ <small id="total-data-month">{{$monthYear}}</small> ]: <b id="total-month"> {{$total_month}}</b><small>hr</small></td></tr>
                    <tr><td><i class="fa-regular fa-calendar"></i> Hora trabalhada no dia [<small id="total-dia"></small>]: <b id="total-dia-hora">{{$total_day}}</b></td></tr>
                    <tr><td style="padding: 0px 0px 10px 10px"><i class="fa fa-user" aria-hidden="true"></i> Funcionário: <b>{{$getPresencamonth[0]->nome_funcionario}}</b>  </td></tr>
                </thead>
            </table>
        </div> --}}
</main>