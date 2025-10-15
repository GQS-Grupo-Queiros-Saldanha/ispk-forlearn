<title>Históricos | forLEARN® by GQS</title>
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .list-group li button {
            border: none;
            background: none;
            outline-style: none;
            transition: all 0.5s;
        }

        .list-group li button:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            font-weight: bold
        }

        .subLink {
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }

        .subLink:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            border-bottom: #dfdfdf 1px solid;
        }

        .fa-arrow-up {
            background-color: #21e821;
            padding: 4px;
            font-size: 10px;
            margin-right: 3px;
            border-radius: 2px;
        }

        .fa-arrow-down {
            background-color: #ff20108c;
            padding: 4px;
            font-size: 10px;
            margin-right: 3px;
            border-radius: 2px;
        }
    </style>


    <div class="content-panel" style="padding:0">
        @include('Payments::requests.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item" aria-current="page">
                                   <a href="https://dev.forlearn.ao/pt/payments/requests">
                                    Tesouraria
                                    </a> 
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Histórico pagamentos de hoje
                                </li>
                            </ol>
                        </div>

                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-9">
                        <h1> Histórico pagamentos de hoje</h1>
                    </div>
                    <div class="col-sm-3">

                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="col-12">

                    <div class="row" hidden>

                        <div class="col-6 div-anolectivo">
                            <div class="form-group col">
                                <label>Estudante</label>

                                <select name="requerimento_tipo" id="requerimento_tipo"
                                    class="selectpicker form-control form-control-sm" data-search="true"
                                    style="width: 100%; !important" disabled>
                                    <option value="0">Zacarias</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 div-anolectivo">
                            <div class="form-group col">
                                <label>Tipo</label>

                                <select name="tipo" id="tipo" class="selectpicker form-control form-control-sm"
                                    data-search="true" style="width: 100%; !important">
                                    <option value="credito">Crédito</option>
                                    <option value="debito">Débito</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="form-group col">

                            
                            @php
                                $recibo_count = 1;
                                $recibo = 0;
                                //$recibo_linhas_count = 1;
                                //$recibo_linhas = 0;
                                //$code = 0;
                            @endphp
                            @foreach ($recipt as $item)
                                @if ($item->transactions_id != $recibo)
                                    @php
                                        $recibo = $item->transactions_id;
                                    @endphp

                                    <h5 class="text-left">Informações do recibo</h5>
                                    <div class="credit-table">
                                        <table id="payment_today-table" class="table table-striped table-hover">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>#</th>
                                                    <th>FACTURA/RECIBO nº</th>
                                                    <th hidden>Referência</th>
                                                    <th>Matrícula nº</th>
                                                    <th>Estudante</th>
                                                    <th>E-mail</th>
                                                    <th>Pagou</th> 
                                                    @isset($item->historic_user_credit_value)
                                                        <th>Saldo em carteira</th>                                                
                                                    @endisset
                                                    <th>Tesoureiro(a)</th>
                                                    <th>Comprovativo</th>
                                                    <th>Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $recibo_count++ }}</td>
                                                    @php $year = substr($item->transaction_created_at, -17, 2) @endphp
                                                    <td>{{ $year }}-{{ $item->code_recib }}</td>
                                                    <td hidden>{{ $item->transaction_info_reference }}</td>
                                                    <td>{{ $item->user_matriculation }}</td>
                                                    <td>{{ $item->user_fullName }}</td>                                                
                                                    <td>{{ $item->user_email }}</td>
                                                    <td>{{ number_format($item->transaction_value_pagou, 2, ',', '.') . ' kz' }}</td>
                                                    @isset($item->historic_user_credit_value)
                                                        <td>{{ number_format($item->historic_user_credit_value, 2, ',', '.') . ' kz' }}</td>
                                                    @endisset
                                                    <td>{{ $item->tesoureiro_nome }}</td>
                                                    <td class="text-center">
                                                        <a href="https://dev.forlearn.ao{{ $item->path }}"
                                                            class="btn btn-info btn-sm" target="blank">
                                                            @icon('fas fa-file-pdf')
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->transaction_created_at }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>                                    
                                    
                                        
                                    <h4 hidden class="text-left">Informações do(s) emolumentos</h4>
                                    @php
                                        $recibo_linhas_count = 1;
                                        $recibo_linhas = 0;
                                        //$code = 0;
                                    @endphp
                                    <div class="credit-table">
                                        <table id="payment_today-table" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Emolumento</th>
                                                    <th>Valor</th>
                                                    <th>Multa</th>
                                                    <th>Total a pagar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($recipt as $item)
                                                    @if ($item->transactions_id === $recibo)
                                                        <tr>
                                                            @if ($item->article_req_id != $recibo_linhas)

                                                                @php $recibo_linhas = $item->article_req_id @endphp
                                                                <td>{{ $recibo_linhas_count++ }}</td>
                                                                @if ($item->article_month)
                                                                    @php                                                            
                                                                        $month = getLocalizedMonths()[$item->article_month - 1]["display_name"];                                                                        
                                                                    @endphp
                                                                    <td>{{ $item->currentTranslation->display_name }} ({{ $month }} {{ $item->article_year }})</td>                                                                
                                                                @endif
                                                                
                                                                <td>{{ number_format($item->article_base_value_custou, 2, ',', '.') . ' kz' }}</td>                                            
                                                                <td>{{ number_format($item->article_extra_fees_value_multa, 2, ',', '.') . ' kz' }}</td>
                                                                <td>{{ number_format($item->transaction_article_request_value_total, 2, ',', '.') . ' kz' }}</td>
                                                            
                                                            @endif
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>                                                                        


                                @endif

                                <hr>

                            @endforeach

                            <div hidden class="credit-table" id="pauta_disciplina">
                                <h4 id="titulo_semestre"></h4>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr style="text-align: center">                                            
                                            <th>FACTURA/RECIBO nº</th>
                                            <th>Matrícula nº</th>
                                            <th>Estudante</th>
                                            <th>E-mail</th>
                                            <th>Quantia</th>
                                            <th>Tesoureiro(a)</th>
                                            <th>Comprovativo</th>
                                            <th>Data</th>
                                        </tr>

                                        <tr id="listaMenu" style="text-align: center">
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="lista_tr">
    
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>



                </div>
            </div>
        </div>

    </div>


    </div>
@endsection
@section('scripts')
    @parent
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}

    <script>
        $(function() {
            
            console.log(2023);
            var data = '2022-11-15'

            $.ajax({
                url: "/payments/pagamentos_dia/" + data,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {
                 
                //console.log(data);
                var tabelaheader = "";
                var tabelabody = "";

                if (data.length != 0) {
                    var lista = data['data'];
                    var recibo = 0;

                    $.each(lista, function(index, item) {
                        //
                        if (item.transactions_id != recibo){
                            
                            recibo = item.transactions_id;
                            tabelaheader += "<th>"
                            tabelaheader += item.code;
                            tabelaheader += "</th>"
                            tabelaheader += "<th>"
                            tabelaheader += item.user_matriculation;
                            tabelaheader += "</th>"
                            tabelaheader += "<th>"
                            tabelaheader += item.user_fullName;
                            tabelaheader += "</th>"
                            tabelaheader += "<th>"
                            tabelaheader += item.user_email;
                            tabelaheader += "</th>"
                            tabelaheader += "<th>"
                            tabelaheader += Number.parseFloat(item.transaction_value_pagou);
                            tabelaheader += "Kwz </th>"
                            tabelaheader += "<th>"
                            tabelaheader += item.tesoureiro_nome;
                            tabelaheader += "</th>"
                            tabelaheader += "<th>"
                            tabelaheader += <a href="https://dev.forlearn.ao"+'item.path'
                                                    class="btn btn-info btn-sm" target="blank">
                                                    @icon('fas fa-file-pdf')
                                                </a>;
                            tabelaheader += "</th>"
                            tabelaheader += "<th>"
                            tabelaheader += item.transaction_created_at;
                            tabelaheader += "</th>"
                            
                            $("#listaMenu").append(tabelaheader);
                            
                            console.log(item)
                        }
                    })
                }

            });

        });
    </script>

@endsection
